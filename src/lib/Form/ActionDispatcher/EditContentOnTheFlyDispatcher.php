<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\ActionDispatcher;

use Ibexa\Contracts\AdminUi\Event\ContentOnTheFlyEvents;
use EzSystems\EzPlatformContentForms\Form\ActionDispatcher\ContentDispatcher;

class EditContentOnTheFlyDispatcher extends ContentDispatcher
{
    protected function getActionEventBaseName(): string
    {
        return ContentOnTheFlyEvents::CONTENT_EDIT;
    }
}

class_alias(EditContentOnTheFlyDispatcher::class, 'EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\EditContentOnTheFlyDispatcher');
