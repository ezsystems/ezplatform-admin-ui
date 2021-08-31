<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    const BTN_TRIGGER_CLASS = 'ibexa-btn--trigger';
    const BTN_DISABLED_ATTR = ['disabled' => 'disabled'];

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ContentService $contentService,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        TranslatorInterface $translator
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
        $this->translator = $translator;
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
        $parentLocation = $options['parent_location'];
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $options['content_type'];
        $parentContentType = $parentLocation->getContent()->getContentType();
        /** @var \eZ\Publish\API\Repository\Values\Content\Language $language */
        $language = $options['language'];
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $contentCreateStruct = $this->createContentCreateStruct($parentLocation, $contentType, $language);
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocation->id);

        $canPublish = $this->permissionResolver->canUser('content', 'publish', $contentCreateStruct, [$locationCreateStruct]);
        $canCreate = $this->permissionResolver->canUser('content', 'create', $contentCreateStruct, [$locationCreateStruct]) && $parentContentType->isContainer;
        $canPreview = $this->permissionResolver->canUser('content', 'versionread', $contentCreateStruct, [$locationCreateStruct]);

        $publishAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_publish',
        ];
        $createAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_saveDraft',
        ];
        $previewAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_preview',
        ];

        $menu->setChildren([
            self::ITEM__PUBLISH => $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => $canCreate && $canPublish
                        ? $publishAttributes
                        : array_merge($publishAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => [
                        'orderNumber' => 10,
                    ],
                ]
            ),
            self::ITEM__SAVE_DRAFT => $this->createMenuItem(
                self::ITEM__SAVE_DRAFT,
                [
                    'attributes' => $canCreate
                        ? $createAttributes
                        : array_merge($createAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => [
                        'orderNumber' => 50,
                    ],
                ]
            ),
            self::ITEM__PREVIEW => $this->createMenuItem(
                self::ITEM__PREVIEW,
                [
                    'attributes' => $canPreview
                        ? $previewAttributes
                        : array_merge($previewAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => [
                        'orderNumber' => 60,
                    ],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'attributes' => [
                        'class' => self::BTN_TRIGGER_CLASS,
                        'data-click' => '#ezplatform_content_forms_content_edit_cancel',
                    ],
                    'extras' => [
                        'orderNumber' => 70,
                    ],
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

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentCreateStruct
     */
    private function createContentCreateStruct(Location $location, ContentType $contentType, Language $language): ContentCreateStruct
    {
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language->languageCode);
        $contentCreateStruct->sectionId = $location->contentInfo->sectionId;

        return $contentCreateStruct;
    }
}
