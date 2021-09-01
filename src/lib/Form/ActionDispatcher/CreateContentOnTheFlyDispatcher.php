<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\ActionDispatcher;

use Ibexa\Contracts\AdminUi\Event\ContentOnTheFlyEvents;
use EzSystems\EzPlatformContentForms\Form\ActionDispatcher\ContentDispatcher;

class CreateContentOnTheFlyDispatcher extends ContentDispatcher
{
    protected function getActionEventBaseName(): string
    {
        return ContentOnTheFlyEvents::CONTENT_CREATE;
    }
}

class_alias(CreateContentOnTheFlyDispatcher::class, 'EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateContentOnTheFlyDispatcher');
