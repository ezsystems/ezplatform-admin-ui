<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Event;

final class FormEvents
{
    /**
     * Base name for ContentType update processing events.
     */
    public const CONTENT_TYPE_UPDATE = 'contentType.update';

    /**
     * Triggered when adding a FieldDefinition to the ContentTypeDraft.
     */
    public const CONTENT_TYPE_ADD_FIELD_DEFINITION = 'contentType.update.addFieldDefinition';

    /**
     * Triggered when removing a FieldDefinition from the ContentTypeDraft.
     */
    public const CONTENT_TYPE_REMOVE_FIELD_DEFINITION = 'contentType.update.removeFieldDefinition';

    /**
     * Triggered when saving the draft + publishing the ContentType.
     */
    public const CONTENT_TYPE_PUBLISH = 'contentType.update.publishContentType';

    /**
     * Triggered when removing the draft (e.g. "cancel" action).
     */
    public const CONTENT_TYPE_REMOVE_DRAFT = 'contentType.update.removeDraft';
}
