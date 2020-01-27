<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
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
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension */
    private $udwExtension;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface */
    private $permissionChecker;

    /** @var array */
    private $userContentTypeIdentifier;

    /** @var array */
    private $userGroupContentTypeIdentifier;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ConfigResolver $udwConfigResolver,
        ConfigResolverInterface $configResolver,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        UniversalDiscoveryExtension $udwExtension,
        ContentService $contentService,
        LocationService $locationService,
        PermissionCheckerInterface $permissionChecker,
        TranslatorInterface $translator,
        array $userContentTypeIdentifier,
        array $userGroupContentTypeIdentifier
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->udwConfigResolver = $udwConfigResolver;
        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
        $this->udwExtension = $udwExtension;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->permissionChecker = $permissionChecker;
        $this->translator = $translator;
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
        $this->userGroupContentTypeIdentifier = $userGroupContentTypeIdentifier;
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
        $canDelete = $this->permissionResolver->canUser(
            'content',
            'remove',
            $content
        );
        $canTrashLocation = $this->permissionResolver->canUser(
            'content',
            'remove',
            $location->getContentInfo(),
            [$location]
        );

        $createAttributes = [
            'class' => 'ez-btn--extra-actions ez-btn--create',
            'title' => $this->translator->trans(
                /** @Ignore */ self::ITEM__CREATE,
                [],
                'menu'
            ),
            'data-actions' => 'create',
            'data-focus-element' => '.ez-instant-filter__input',
            'data-extra-classes' => 'ez-tooltip--medium',
            'data-placement' => 'left',
        ];
        $sendToTrashAttributes = [
            'title' => $this->translator->trans(
                /** @Ignore */ self::ITEM__SEND_TO_TRASH,
                [],
                'menu'
            ),
            'data-toggle' => 'modal',
            'data-target' => '#trash-location-modal',
            'data-extra-classes' => 'ez-tooltip--medium',
            'data-placement' => 'left',
        ];
        $copySubtreeAttributes = [
            'class' => 'ez-btn--udw-copy-subtree',
            'title' => $this->translator->trans(
                /** @Ignore */ self::ITEM__COPY_SUBTREE,
                [],
                'menu'
            ),
            'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single_container'),
            'data-root-location' => $startingLocationId,
            'data-extra-classes' => 'ez-tooltip--medium',
            'data-placement' => 'left',
        ];

        $copyLimit = $this->configResolver->getParameter(
            'subtree_operations.copy_subtree.limit'
        );
        $canCopySubtree = (new IsWithinCopySubtreeLimit(
            $copyLimit,
            $this->searchService
        ))->and((new IsRoot())->not())->isSatisfiedBy($location);

        $contentIsUser = (new ContentTypeIsUser($this->userContentTypeIdentifier))->isSatisfiedBy($contentType);
        $contentIsUserGroup = (new ContentTypeIsUserGroup($this->userGroupContentTypeIdentifier))->isSatisfiedBy($contentType);

        $menu->setChildren([
            self::ITEM__CREATE => $this->createMenuItem(
                self::ITEM__CREATE,
                [
                    'extras' => ['icon' => 'create'],
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
                    'extras' => ['icon' => 'move'],
                    'attributes' => [
                        'class' => 'btn--udw-move',
                        'title' => $this->translator->trans(
                            /** @Ignore */ self::ITEM__MOVE,
                            [],
                            'menu'
                        ),
                        'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single_container'),
                        'data-root-location' => $startingLocationId,
                        'data-extra-classes' => 'ez-tooltip--medium',
                        'data-placement' => 'left',
                    ],
                ]
            )
        );
        if (!$contentIsUser && !$contentIsUserGroup) {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__COPY,
                    [
                        'extras' => ['icon' => 'copy'],
                        'attributes' => [
                            'class' => 'btn--udw-copy',
                            'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single_container'),
                            'data-root-location' => $startingLocationId,
                            'data-extra-classes' => 'ez-tooltip--medium',
                            'data-placement' => 'left',
                            'title' => $this->translator->trans(
                                /** @Ignore */ self::ITEM__COPY,
                                [],
                                'menu'
                            ),
                        ],
                    ]
                )
            );

            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__COPY_SUBTREE,
                    [
                        'extras' => ['icon' => 'copy-subtree'],
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
                        'extras' => ['icon' => 'trash'],
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
                        'extras' => ['icon' => 'trash-send'],
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
            'title' => $this->translator->trans(
                /** @Ignore */ self::ITEM__EDIT,
                [],
                'menu'
            ),
            'data-actions' => 'edit',
            'data-extra-classes' => 'ez-tooltip--medium',
            'data-placement' => 'left',
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
                        'extras' => ['icon' => 'edit'],
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
                        'extras' => ['icon' => 'edit'],
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
                    'extras' => ['icon' => 'reveal'],
                    'attributes' => [
                        'class' => 'ez-btn--reveal',
                        'data-actions' => 'reveal',
                        'data-extra-classes' => 'ez-tooltip--medium',
                        'data-placement' => 'left',
                        'title' => $this->translator->trans(
                            /** @Ignore */ self::ITEM__REVEAL,
                            [],
                            'menu'
                        ),
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
                    'extras' => ['icon' => 'hide'],
                    'attributes' => [
                        'class' => 'ez-btn--hide',
                        'title' => $this->translator->trans(
                            /** @Ignore */ self::ITEM__HIDE,
                            [],
                            'menu'
                        ),
                        'data-actions' => 'hide',
                        'data-extra-classes' => 'ez-tooltip--medium',
                        'data-placement' => 'left',
                    ],
                ]
            )
        );
    }
}
