<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\SPI\Limitation\Target;
use eZ\Publish\SPI\Limitation\Target\Builder\VersionBuilder;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUserGroup;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsRoot;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsWithinCopySubtreeLimit;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Location View contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class ContentRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__CREATE = 'content__sidebar_right__create';
    const ITEM__EDIT = 'content__sidebar_right__edit';
    const ITEM__SEND_TO_TRASH = 'content__sidebar_right__send_to_trash';
    const ITEM__COPY = 'content__sidebar_right__copy';
    const ITEM__COPY_SUBTREE = 'content__sidebar_right__copy_subtree';
    const ITEM__MOVE = 'content__sidebar_right__move';
    const ITEM__DELETE = 'content__sidebar_right__delete';
    const ITEM__HIDE = 'content__sidebar_right__hide';
    const ITEM__REVEAL = 'content__sidebar_right__reveal';

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver */
    private $udwConfigResolver;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension */
    private $udwExtension;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface */
    private $permissionChecker;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ConfigResolver $udwConfigResolver,
        ConfigResolverInterface $configResolver,
        SearchService $searchService,
        UniversalDiscoveryExtension $udwExtension,
        PermissionCheckerInterface $permissionChecker
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->udwConfigResolver = $udwConfigResolver;
        $this->searchService = $searchService;
        $this->udwExtension = $udwExtension;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::CONTENT_SIDEBAR_RIGHT;
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var Location $location */
        $location = $options['location'];
        /** @var ContentType $contentType */
        $contentType = $options['content_type'];
        /** @var Content $content */
        $content = $options['content'];
        /** @var ItemInterface|ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');
        $startingLocationId = $this->udwConfigResolver->getConfig('default')['starting_location_id'];

        $lookupLimitationsResult = $this->permissionChecker->getContentCreateLimitations($location);
        $canCreate = $lookupLimitationsResult->hasAccess && $contentType->isContainer;
        $canEdit = $this->permissionResolver->canUser(
            'content',
            'edit',
            $location->getContentInfo(),
            [
                (new VersionBuilder())
                    ->translateToAnyLanguageOf($content->getVersionInfo()->languageCodes)
                    ->build(),
            ]
        );
        $translations = $content->getVersionInfo()->languageCodes;
        $target = (new Target\Version())->deleteTranslations($translations);
        $canDelete = $this->permissionResolver->canUser(
            'content',
            'remove',
            $content,
            [$target]
        );
        $canTrashLocation = $this->permissionResolver->canUser(
            'content',
            'remove',
            $location->getContentInfo(),
            [$location, $target]
        );

        $createAttributes = [
            'class' => 'ez-btn--extra-actions ez-btn--create',
            'data-actions' => 'create',
            'data-focus-element' => '.ez-instant-filter__input',
        ];
        $sendToTrashAttributes = [
            'data-toggle' => 'modal',
            'data-target' => '#trash-location-modal',
        ];
        $copySubtreeAttributes = [
            'class' => 'ez-btn--udw-copy-subtree',
            'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single_container'),
            'data-root-location' => $startingLocationId,
        ];

        $copyLimit = $this->configResolver->getParameter(
            'subtree_operations.copy_subtree.limit'
        );
        $canCopySubtree = (new IsWithinCopySubtreeLimit(
            $copyLimit,
            $this->searchService
        ))->and((new IsRoot())->not())->isSatisfiedBy($location);

        $contentIsUser = (new ContentTypeIsUser($this->configResolver->getParameter('user_content_type_identifier')))
            ->isSatisfiedBy($contentType);
        $contentIsUserGroup = (new ContentTypeIsUserGroup($this->configResolver->getParameter('user_group_content_type_identifier')))
            ->isSatisfiedBy($contentType);

        $menu->setChildren([
            self::ITEM__CREATE => $this->createMenuItem(
                self::ITEM__CREATE,
                [
                    'extras' => ['icon' => 'create', 'orderNumber' => 10],
                    'attributes' => $canCreate
                        ? $createAttributes
                        : array_merge($createAttributes, ['disabled' => 'disabled']),
                ]
            ),
        ]);

        $this->addEditMenuItem($menu, $contentIsUser, $canEdit);

        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__MOVE,
                [
                    'extras' => ['icon' => 'move', 'orderNumber' => 30],
                    'attributes' => [
                        'class' => 'btn--udw-move',
                        'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single_container'),
                        'data-root-location' => $startingLocationId,
                    ],
                ]
            )
        );
        if (!$contentIsUser && !$contentIsUserGroup) {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__COPY,
                    [
                        'extras' => ['icon' => 'copy', 'orderNumber' => 40],
                        'attributes' => [
                            'class' => 'btn--udw-copy',
                            'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single_container'),
                            'data-root-location' => $startingLocationId,
                        ],
                    ]
                )
            );

            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__COPY_SUBTREE,
                    [
                        'extras' => ['icon' => 'copy-subtree', 'orderNumber' => 50],
                        'attributes' => $canCopySubtree
                            ? $copySubtreeAttributes
                            : array_merge($copySubtreeAttributes, ['disabled' => 'disabled']),
                    ]
                )
            );
        }

        if ($content->getVersionInfo()->getContentInfo()->isHidden) {
            $this->addRevealMenuItem($menu);
        } else {
            $this->addHideMenuItem($menu);
        }

        if ($contentIsUser && $canDelete) {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__DELETE,
                    [
                        'extras' => ['icon' => 'trash', 'orderNumber' => 70],
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#delete-user-modal',
                        ],
                    ]
                )
            );
        }

        if (!$contentIsUser && 1 !== $location->depth && $canTrashLocation) {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__SEND_TO_TRASH,
                    [
                        'extras' => ['icon' => 'trash-send', 'orderNumber' => 80],
                        'attributes' => $sendToTrashAttributes,
                    ]
                )
            );
        }

        if (1 === $location->depth) {
            $menu[self::ITEM__MOVE]->setAttribute('disabled', 'disabled');
        }

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__CREATE, 'menu'))->setDesc('Create'),
            (new Message(self::ITEM__EDIT, 'menu'))->setDesc('Edit'),
            (new Message(self::ITEM__SEND_TO_TRASH, 'menu'))->setDesc('Send to Trash'),
            (new Message(self::ITEM__COPY, 'menu'))->setDesc('Copy'),
            (new Message(self::ITEM__COPY_SUBTREE, 'menu'))->setDesc('Copy Subtree'),
            (new Message(self::ITEM__MOVE, 'menu'))->setDesc('Move'),
            (new Message(self::ITEM__DELETE, 'menu'))->setDesc('Delete'),
            (new Message(self::ITEM__HIDE, 'menu'))->setDesc('Hide'),
            (new Message(self::ITEM__REVEAL, 'menu'))->setDesc('Reveal'),
        ];
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     * @param bool $contentIsUser
     * @param bool $canEdit
     */
    private function addEditMenuItem(ItemInterface $menu, bool $contentIsUser, bool $canEdit): void
    {
        $editAttributes = [
            'class' => 'ez-btn--extra-actions ez-btn--edit',
            'data-actions' => 'edit',
        ];
        $editUserAttributes = [
            'class' => 'ez-btn--extra-actions ez-btn--edit-user',
            'data-actions' => 'edit-user',
        ];

        if ($contentIsUser) {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__EDIT,
                    [
                        'extras' => ['icon' => 'edit', 'orderNumber' => 20],
                        'attributes' => $canEdit
                            ? $editUserAttributes
                            : array_merge($editUserAttributes, ['disabled' => 'disabled']),
                    ]
                )
            );
        } else {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__EDIT,
                    [
                        'extras' => ['icon' => 'edit', 'orderNumber' => 20],
                        'attributes' => $canEdit
                            ? $editAttributes
                            : array_merge($editAttributes, ['disabled' => 'disabled']),
                    ]
                )
            );
        }
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     */
    private function addRevealMenuItem(ItemInterface $menu): void
    {
        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__REVEAL,
                [
                    'extras' => ['icon' => 'reveal', 'orderNumber' => 60],
                    'attributes' => [
                        'class' => 'ez-btn--reveal',
                        'data-actions' => 'reveal',
                    ],
                ]
            )
        );
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     */
    private function addHideMenuItem(ItemInterface $menu): void
    {
        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__HIDE,
                [
                    'extras' => ['icon' => 'hide', 'orderNumber' => 60],
                    'attributes' => [
                        'class' => 'ez-btn--hide',
                        'data-actions' => 'hide',
                    ],
                ]
            )
        );
    }
}
