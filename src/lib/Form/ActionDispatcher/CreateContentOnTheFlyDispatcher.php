<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\ActionDispatcher;

use EzSystems\EzPlatformAdminUi\Event\ContentOnTheFlyEvents;
use EzSystems\EzPlatformContentForms\Form\ActionDispatcher\ContentDispatcher;

class CreateContentOnTheFlyDispatcher extends ContentDispatcher
{
    protected function getActionEventBaseName(): string
    {
        return ContentOnTheFlyEvents::CONTENT_CREATE;
    }
}
