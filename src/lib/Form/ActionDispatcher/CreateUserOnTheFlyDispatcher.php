<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\ActionDispatcher;

use Ibexa\Contracts\AdminUi\Event\UserOnTheFlyEvents;
use EzSystems\EzPlatformContentForms\Form\ActionDispatcher\ContentDispatcher;

class CreateUserOnTheFlyDispatcher extends ContentDispatcher
{
    protected function getActionEventBaseName(): string
    {
        return UserOnTheFlyEvents::USER_CREATE;
    }
}

class_alias(CreateUserOnTheFlyDispatcher::class, 'EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateUserOnTheFlyDispatcher');
