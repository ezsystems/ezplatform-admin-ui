<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Event;

final class RepositoryFormEvents
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

    /**
     * Triggered when updating a ContentType group.
     */
    public const CONTENT_TYPE_GROUP_UPDATE = 'contentType.group.update';

    /**
     * Base name for Content edit processing events.
     */
    public const CONTENT_EDIT = 'content.edit';

    /**
     * Triggered when saving a content draft.
     */
    public const CONTENT_SAVE_DRAFT = 'content.edit.saveDraft';

    /**
     * Triggered when publishing a content.
     */
    public const CONTENT_PUBLISH = 'content.edit.publish';

    /**
     * Triggered when canceling a content edition.
     */
    public const CONTENT_CANCEL = 'content.edit.cancel';

    /**
     * Base name for Role update processing events.
     */
    public const ROLE_UPDATE = 'role.update';

    /**
     * Triggered when saving the role.
     */
    public const ROLE_SAVE = 'role.update.saveRole';

    /**
     * Triggered when removing the draft (e.g. "cancel" action).
     */
    public const ROLE_REMOVE_DRAFT = 'role.update.removeDraft';

    /**
     * Base name for Policy update processing events.
     */
    public const POLICY_UPDATE = 'policy.update';

    /**
     * Triggered when saving the policy.
     */
    public const POLICY_SAVE = 'policy.update.savePolicy';

    /**
     * Triggered when canceling policy edition.
     */
    public const POLICY_REMOVE_DRAFT = 'policy.update.removeDraft';

    /**
     * Triggered when updating a section.
     */
    public const SECTION_UPDATE = 'section.update';

    /**
     * Triggered when updating a language.
     */
    public const LANGUAGE_UPDATE = 'language.update';

    /**
     * Base name for User edit processing events.
     */
    public const USER_EDIT = 'user.edit';

    /**
     * Triggered when saving an user.
     */
    public const USER_UPDATE = 'user.edit.update';

    /**
     * Triggered when creating an user.
     */
    public const USER_CREATE = 'user.edit.create';

    /**
     * Triggered when registering an user.
     */
    public const USER_REGISTER = 'user.edit.register';

    /**
     * Triggered when canceling a user edition.
     */
    public const USER_CANCEL = 'user.edit.cancel';
}
