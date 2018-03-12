<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\Exceptions as ApiExceptions;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Content Edit contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class ContentEditRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /** @var NonAdminSiteaccessResolver */
    private $siteaccessResolver;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        NonAdminSiteaccessResolver $siteaccessResolver
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->siteaccessResolver = $siteaccessResolver;
    }

    /* Menu items */
    const ITEM__PUBLISH = 'content_edit__sidebar_right__publish';
    const ITEM__SAVE_DRAFT = 'content_edit__sidebar_right__save_draft';
    const ITEM__PREVIEW = 'content_edit__sidebar_right__preview';
    const ITEM__CANCEL = 'content_edit__sidebar_right__cancel';

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

        $items = [
            self::ITEM__PUBLISH => $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => [
                        'class' => 'btn--trigger',
                        'data-click' => '#ezrepoforms_content_edit_publish',
                    ],
                    'extras' => ['icon' => 'publish'],
                ]
            ),
            self::ITEM__SAVE_DRAFT => $this->createMenuItem(
                self::ITEM__SAVE_DRAFT,
                [
                    'attributes' => [
                        'class' => 'btn--trigger',
                        'data-click' => '#ezrepoforms_content_edit_saveDraft',
                    ],
                    'extras' => ['icon' => 'save'],
                ]
            ),
        ];

        $siteaccesses = $this->siteaccessResolver->getSiteaccessesForLocation(
            $location,
            $content->getVersionInfo()->versionNo,
            $language->languageCode
        );
        $items[self::ITEM__PREVIEW] = $this->createMenuItem(
            self::ITEM__PREVIEW,
            [
                'attributes' => [
                    'class' => 'btn--trigger',
                    'data-click' => '#ezrepoforms_content_edit_preview',
                    'disabled' => empty($siteaccesses),
                ],
                'extras' => ['icon' => 'view-desktop'],
            ]
        );

        $items[self::ITEM__CANCEL] = $this->createMenuItem(
            self::ITEM__CANCEL,
            [
                'attributes' => [
                    'class' => 'btn--trigger',
                    'data-click' => '#ezrepoforms_content_edit_cancel',
                ],
                'extras' => ['icon' => 'circle-close'],
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
}
