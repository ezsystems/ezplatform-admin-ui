<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Event;

final class ContentOnTheFlyEvents
{
    /** @var string */
    public const CONTENT_CREATE = 'ezplatform.content_on_the_fly.create';

    /** @var string */
    public const CONTENT_CREATE_PUBLISH = 'ezplatform.content_on_the_fly.create.publish';

    /** @var string */
    public const CONTENT_EDIT = 'ezplatform.content_on_the_fly.edit';

    /** @var string */
    public const CONTENT_EDIT_PUBLISH = 'ezplatform.content_on_the_fly.edit.publish';
}
