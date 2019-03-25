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
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentHaveAssetRelation;
use EzSystems\EzPlatformAdminUi\Specification\Location\HasChildren;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUserGroup;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsRoot;
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

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

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

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \EzSystems\EzPlatformAdminUiBundle\Templating\Twig\UniversalDiscoveryExtension $udwExtension
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface $permissionChecker
     * @param array $userContentTypeIdentifier
     * @param array $userGroupContentTypeIdentifier
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        UniversalDiscoveryExtension $udwExtension,
        ContentService $contentService,
        LocationService $locationService,
        PermissionCheckerInterface $permissionChecker,
        array $userContentTypeIdentifier,
        array $userGroupContentTypeIdentifier
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
        $this->udwExtension = $udwExtension;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->permissionChecker = $permissionChecker;
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
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
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

        $hasAccess = $this->permissionResolver->hasAccess('content', 'create');
        $canCreateInLocation = $this->permissionChecker->canCreateInLocation($location, $hasAccess);

        $canCreate = $canCreateInLocation && $contentType->isContainer;
        $canEdit = $this->permissionResolver->canUser(
            'content',
            'edit',
            $location->getContentInfo()
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
            'data-root-location' => $this->configResolver->getParameter(
                'universal_discovery_widget_module.default_location_id'
            ),
        ];

        $copyLimit = $this->configResolver->getParameter(
            'subtree_operations.copy_subtree.limit'
        );
        $canCopySubtree = (new IsWithinCopySubtreeLimit(
            $copyLimit,
            $this->searchService
        ))->and((new IsRoot())->not())->isSatisfiedBy($location);

        if ((new ContentHaveAssetRelation($this->contentService))->isSatisfiedBy($content)) {
            $sendToTrashAttributes['data-target'] = '#trash-with-asset-modal';
        }

        $hasChildren = (new HasChildren($this->locationService))->isSatisfiedBy($location);

        if ($contentType->isContainer && $hasChildren) {
            $sendToTrashAttributes['data-target'] = '#trash-container-modal';
        }

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
                        'data-udw-config' => $this->udwExtension->renderUniversalDiscoveryWidgetConfig('single_container'),
                        'data-root-location' => $this->configResolver->getParameter(
                            'universal_discovery_widget_module.default_location_id'
                        ),
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
                            'data-root-location' => $this->configResolver->getParameter(
                                'universal_discovery_widget_module.default_location_id'
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
}
