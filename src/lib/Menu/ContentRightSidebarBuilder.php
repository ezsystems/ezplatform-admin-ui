<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\SPI\Limitation\Target;
use eZ\Publish\SPI\Limitation\Target\Builder\VersionBuilder;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUserGroup;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsRoot;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsWithinCopySubtreeLimit;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver;
use EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension;
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
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $options['location'];
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $options['content_type'];
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $options['content'];
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');
        $startingLocationId = $this->udwConfigResolver->getConfig('default')['starting_location_id'];

        $lookupLimitationsResult = $this->permissionChecker->getContentCreateLimitations($location);
        $canCreate = $lookupLimitationsResult->hasAccess && $contentType->isContainer;
        $uwdConfig = $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single_container');
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
        $canHide = $this->permissionResolver->canUser(
            'content',
            'hide',
            $content,
            [$target]
        );
        $hasCreatePermission = $this->hasCreatePermission();
        $canCopy = $this->canCopy($hasCreatePermission);
        $canCopySubtree = $this->canCopySubtree($location, $hasCreatePermission);
        $createAttributes = [
            'class' => 'ibexa-btn--extra-actions ibexa-btn--create ibexa-btn--primary',
            'data-actions' => 'create',
            'data-focus-element' => '.ez-instant-filter__input',
        ];
        $sendToTrashAttributes = [
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#trash-location-modal',
        ];
        $copySubtreeAttributes = [
            'class' => 'ibexa-btn--udw-copy-subtree',
            'data-udw-config' => $uwdConfig,
            'data-root-location' => $startingLocationId,
        ];
        $moveAttributes = [
            'class' => 'ibexa-btn--udw-move',
            'data-udw-config' => $uwdConfig,
            'data-root-location' => $startingLocationId,
        ];
        $copyAttributes = [
            'class' => 'ibexa-btn--udw-copy',
            'data-udw-config' => $uwdConfig,
            'data-root-location' => $startingLocationId,
        ];

        $contentIsUser = (new ContentTypeIsUser($this->configResolver->getParameter('user_content_type_identifier')))
            ->isSatisfiedBy($contentType);
        $contentIsUserGroup = (new ContentTypeIsUserGroup($this->configResolver->getParameter('user_group_content_type_identifier')))
            ->isSatisfiedBy($contentType);

        $menu->setChildren([
            self::ITEM__CREATE => $this->createMenuItem(
                self::ITEM__CREATE,
                [
                    'extras' => ['icon' => 'create', 'orderNumber' => 10, 'primary' => true],
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
                    'extras' => ['orderNumber' => 30],
                    'attributes' => $hasCreatePermission
                        ? $moveAttributes
                        : array_merge($moveAttributes, ['disabled' => 'disabled']),
                ]
            )
        );
        if (!$contentIsUser && !$contentIsUserGroup) {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__COPY,
                    [
                        'extras' => ['orderNumber' => 40],
                        'attributes' => $canCopy
                            ? $copyAttributes
                            : array_merge($copyAttributes, ['disabled' => 'disabled']),
                    ]
                )
            );

            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__COPY_SUBTREE,
                    [
                        'extras' => ['orderNumber' => 50],
                        'attributes' => $canCopySubtree
                            ? $copySubtreeAttributes
                            : array_merge($copySubtreeAttributes, ['disabled' => 'disabled']),
                    ]
                )
            );
        }

        if ($content->getVersionInfo()->getContentInfo()->isHidden) {
            $this->addRevealMenuItem($menu, $canHide);
        } else {
            $this->addHideMenuItem($menu, $canHide);
        }

        if ($contentIsUser && $canDelete) {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__DELETE,
                    [
                        'extras' => ['orderNumber' => 70],
                        'attributes' => [
                            'data-bs-toggle' => 'modal',
                            'data-bs-target' => '#delete-user-modal',
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
                        'extras' => ['orderNumber' => 80],
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
            (new Message(self::ITEM__CREATE, 'menu'))->setDesc('Create content'),
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
            'class' => 'ibexa-btn--extra-actions ibexa-btn--edit',
            'data-actions' => 'edit',
        ];
        $editUserAttributes = [
            'class' => 'ibexa-btn--extra-actions ibexa-btn--edit-user',
            'data-actions' => 'edit-user',
        ];

        if ($contentIsUser) {
            $menu->addChild(
                $this->createMenuItem(
                    self::ITEM__EDIT,
                    [
                        'extras' => ['orderNumber' => 20],
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
                        'extras' => ['orderNumber' => 20],
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
    private function addRevealMenuItem(ItemInterface $menu, bool $canHide): void
    {
        $attributes = [
            'class' => 'ibexa-btn--reveal',
            'data-actions' => 'reveal',
        ];

        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__REVEAL,
                [
                    'extras' => ['orderNumber' => 60],
                    'attributes' => $canHide
                        ? $attributes
                        : array_merge($attributes, ['disabled' => 'disabled']),
                ]
            )
        );
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     */
    private function addHideMenuItem(ItemInterface $menu, bool $canHide): void
    {
        $attributes = [
            'class' => 'ibexa-btn--hide',
            'data-actions' => 'hide',
        ];

        $menu->addChild(
            $this->createMenuItem(
                self::ITEM__HIDE,
                [
                    'extras' => ['orderNumber' => 60],
                    'attributes' => $canHide
                        ? $attributes
                        : array_merge($attributes, ['disabled' => 'disabled']),
                ]
            )
        );
    }

    private function hasCreatePermission(): bool
    {
        $createPolicies = $this->permissionResolver->hasAccess(
            'content',
            'create'
        );

        return !is_bool($createPolicies) || $createPolicies;
    }

    private function canCopy(bool $hasCreatePermission): bool
    {
        $manageLocationsPolicies = $this->permissionResolver->hasAccess(
            'content',
            'manage_locations'
        );

        $hasManageLocationsPermission = !is_bool($manageLocationsPolicies) || $manageLocationsPolicies;

        return $hasCreatePermission && $hasManageLocationsPermission;
    }

    private function canCopySubtree(Location $location, bool $hasCreatePermission): bool
    {
        $copyLimit = $this->configResolver->getParameter(
            'subtree_operations.copy_subtree.limit'
        );

        $canCopySubtree = (new IsWithinCopySubtreeLimit(
            $copyLimit,
            $this->searchService
        ))->and((new IsRoot())->not())->isSatisfiedBy($location);

        return $canCopySubtree && $hasCreatePermission;
    }
}
