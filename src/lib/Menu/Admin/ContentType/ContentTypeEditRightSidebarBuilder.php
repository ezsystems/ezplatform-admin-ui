<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu\Admin\ContentType;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;

/**
 * KnpMenuBundle Menu Builder service implementation for AdminUI Section Edit contextual sidebar menu.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class ContentTypeEditRightSidebarBuilder extends AbstractContentTypeRightSidebarBuilder
{
    /* Menu items */
    const ITEM__SAVE = 'content_type_edit__sidebar_right__save';
    const ITEM__CANCEL = 'content_type_edit__sidebar_right__cancel';

    protected function getConfigureEventName(): string
    {
        return ConfigureMenuEvent::CONTENT_TYPE_EDIT_SIDEBAR_RIGHT;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__SAVE, 'menu'))->setDesc('Save'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Discard changes'),
        ];
    }

    public function getItemSaveIdentifier(): string
    {
        return self::ITEM__SAVE;
    }

    public function getItemCancelIdentifier(): string
    {
        return self::ITEM__CANCEL;
    }
}
