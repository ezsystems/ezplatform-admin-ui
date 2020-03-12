<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Event;

final class UserOnTheFlyEvents
{
    /** @var string */
    public const USER_CREATE = 'ezplatform.user_on_the_fly.create';

    /** @var string */
    public const USER_CREATE_PUBLISH = 'ezplatform.user_on_the_fly.create.publish';

    /** @var string */
    public const USER_EDIT = 'ezplatform.user_on_the_fly.edit';

    /** @var string */
    public const USER_EDIT_PUBLISH = 'ezplatform.user_on_the_fly.edit.publish';
}