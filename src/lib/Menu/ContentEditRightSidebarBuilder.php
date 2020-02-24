<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\SPI\Limitation\Target;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use eZ\Publish\API\Repository\PermissionResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Content Edit contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class ContentEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    const ITEM__PUBLISH = 'content_edit__sidebar_right__publish';
    const ITEM__SAVE_DRAFT = 'content_edit__sidebar_right__save_draft';
    const ITEM__PREVIEW = 'content_edit__sidebar_right__preview';
    const ITEM__CANCEL = 'content_edit__sidebar_right__cancel';

    const BTN_TRIGGER_CLASS = 'btn--trigger';
    const BTN_DISABLED_ATTR = ['disabled' => 'disabled'];

    /** @var \EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver */
    private $siteaccessResolver;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        NonAdminSiteaccessResolver $siteaccessResolver,
        PermissionResolver $permissionResolver,
        LocationService $locationService,
        TranslatorInterface $translator
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->siteaccessResolver = $siteaccessResolver;
        $this->permissionResolver = $permissionResolver;
        $this->locationService = $locationService;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::CONTENT_EDIT_SIDEBAR_RIGHT;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     *
     * @throws ApiExceptions\InvalidArgumentException
     * @throws ApiExceptions\BadStateException
     * @throws InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var ItemInterface|ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        /** @var Location $location */
        $location = $options['location'];
        /** @var Content $content */
        $content = $options['content'];
        /** @var Language $language */
        $language = $options['language'];
        /** @var Location $parentLocation */
        $parentLocation = $options['parent_location'];

        $target = (new Target\Builder\VersionBuilder())->translateToAnyLanguageOf([$language->languageCode])->build();
        $canPublish = $this->permissionResolver->canUser('content', 'publish', $content, [$target]);
        $canEdit = $this->permissionResolver->canUser('content', 'edit', $content, [$target]);
        $canDelete = $this->permissionResolver->canUser('content', 'versionremove', $content);

        $publishAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_publish',
            'data-extra-classes' => 'ez-tooltip--medium',
            'data-placement' => 'left',
            'title' => $this->translator->trans(
/** @Ignore */ self::ITEM__PUBLISH,
                [],
                'menu'
            ),
        ];
        $editAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_saveDraft',
            'data-extra-classes' => 'ez-tooltip--medium',
            'data-placement' => 'left',
            'title' => $this->translator->trans(
/** @Ignore */ self::ITEM__SAVE_DRAFT,
                [],
                'menu'
            ),
        ];
        $deleteAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_cancel',
            'data-extra-classes' => 'ez-tooltip--medium',
            'data-placement' => 'left',
            'title' => $this->translator->trans(
/** @Ignore */ self::ITEM__CANCEL,
                [],
                'menu'
            ),
        ];

        $items = [
            self::ITEM__PUBLISH => $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => $canEdit && $canPublish
                        ? $publishAttributes
                        : array_merge($publishAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => [
                        'icon' => 'publish',
                        'orderNumber' => 10,
                    ],
                ]
            ),
            self::ITEM__SAVE_DRAFT => $this->createMenuItem(
                self::ITEM__SAVE_DRAFT,
                [
                    'attributes' => $canEdit
                        ? $editAttributes
                        : array_merge($editAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => [
                        'icon' => 'save',
                        'orderNumber' => 50,
                    ],
                ]
            ),
        ];

        $items[self::ITEM__PREVIEW] = $this->getContentPreviewItem(
            $location,
            $content,
            $language,
            $parentLocation
        );

        $items[self::ITEM__CANCEL] = $this->createMenuItem(
            self::ITEM__CANCEL,
            [
                'attributes' => $canDelete
                    ? $deleteAttributes
                    : array_merge($deleteAttributes, self::BTN_DISABLED_ATTR),
                'extras' => [
                    'icon' => 'circle-close',
                    'orderNumber' => 70,
                ],
            ]
        );

        $menu->setChildren($items);

        return $menu;
    }

    /**
     * @return Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__PUBLISH, 'menu'))->setDesc('Publish'),
            (new Message(self::ITEM__SAVE_DRAFT, 'menu'))->setDesc('Save'),
            (new Message(self::ITEM__PREVIEW, 'menu'))->setDesc('Preview'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Delete draft'),
        ];
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function getContentPreviewItem(
        ?Location $location,
        Content $content,
        Language $language,
        Location $parentLocation
    ): ItemInterface {
        $versionNo = $content->getVersionInfo()->versionNo;

        // nonpublished content should use parent location instead because location doesn't exist yet
        if (!$content->contentInfo->published && null === $content->contentInfo->mainLocationId) {
            $location = $parentLocation;
            $versionNo = null;
        }

        $siteaccesses = $this->siteaccessResolver->getSiteaccessesForLocation(
            $location,
            $versionNo,
            $language->languageCode
        );

        $canPreview = $this->permissionResolver->canUser(
            'content',
            'versionread',
            $content,
            [$location ?? $this->locationService->newLocationCreateStruct($parentLocation->id)]
        );

        $previewAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_preview',
            'data-extra-classes' => 'ez-tooltip--medium',
            'data-placement' => 'left',
            'title' => $this->translator->trans(
/** @Ignore */ self::ITEM__PREVIEW,
                [],
                'menu'
            ),
        ];

        return $this->createMenuItem(
            self::ITEM__PREVIEW,
            [
                'attributes' => $canPreview && !empty($siteaccesses)
                    ? $previewAttributes
                    : array_merge($previewAttributes, self::BTN_DISABLED_ATTR),
                'extras' => [
                    'icon' => 'view-desktop',
                    'orderNumber' => 60,
                ],
            ]
        );
    }
}
