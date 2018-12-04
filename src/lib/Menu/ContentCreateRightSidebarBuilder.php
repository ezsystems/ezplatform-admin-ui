<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Content Edit contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class ContentCreateRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__PUBLISH = 'content_create__sidebar_right__publish';
    const ITEM__SAVE_DRAFT = 'content_create__sidebar_right__save_draft';
    const ITEM__PREVIEW = 'content_create__sidebar_right__preview';
    const ITEM__CANCEL = 'content_create__sidebar_right__cancel';

    const BTN_TRIGGER_CLASS = 'btn--trigger';
    const BTN_DISABLED_ATTR = ['disabled' => 'disabled'];

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ContentService $contentService,
        LocationService $locationService,
        ContentTypeService $contentTypeService
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::CONTENT_CREATE_SIDEBAR_RIGHT;
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $parentLocation */
        $parentLocation = $options['parentLocation'];
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $options['content_type'];
        $parentContentType = $parentLocation->getContent()->getContentType();
        /** @var \eZ\Publish\API\Repository\Values\Content\Language $language */
        $language = $options['language'];
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language->languageCode);
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocation->id);

        $canPublish = $this->permissionResolver->canUser('content', 'publish', $contentCreateStruct, [$locationCreateStruct]);
        $canCreate = $this->permissionResolver->canUser('content', 'create', $contentCreateStruct, [$locationCreateStruct]) && $parentContentType->isContainer;
        $canPreview = $this->permissionResolver->canUser('content', 'versionread', $contentCreateStruct, [$locationCreateStruct]);
        $publishAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezrepoforms_content_edit_publish',
        ];
        $createAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezrepoforms_content_edit_saveDraft',
        ];
        $previewAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezrepoforms_content_edit_preview',
        ];

        $menu->setChildren([
            self::ITEM__PUBLISH => $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => $canCreate && $canPublish
                        ? $publishAttributes
                        : array_merge($publishAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => ['icon' => 'publish'],
                ]
            ),
            self::ITEM__SAVE_DRAFT => $this->createMenuItem(
                self::ITEM__SAVE_DRAFT,
                [
                    'attributes' => $canCreate
                        ? $createAttributes
                        : array_merge($createAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => ['icon' => 'save'],
                ]
            ),
            self::ITEM__PREVIEW => $this->createMenuItem(
                self::ITEM__PREVIEW,
                [
                    'attributes' => $canPreview
                        ? $previewAttributes
                        : array_merge($previewAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => ['icon' => 'view-desktop'],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'attributes' => [
                        'class' => self::BTN_TRIGGER_CLASS,
                        'data-click' => '#ezrepoforms_content_edit_cancel',
                    ],
                    'extras' => ['icon' => 'circle-close'],
                ]
            ),
        ]);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__PUBLISH, 'menu'))->setDesc('Publish'),
            (new Message(self::ITEM__SAVE_DRAFT, 'menu'))->setDesc('Save'),
            (new Message(self::ITEM__PREVIEW, 'menu'))->setDesc('Preview'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Cancel'),
        ];
    }
}
