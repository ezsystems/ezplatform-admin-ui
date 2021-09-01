<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\UI\Value\Content;

interface ContentDraftInterface
{
    /**
     * @return bool
     */
    public function isAccessible(): bool;
}

class_alias(ContentDraftInterface::class, 'EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraftInterface');
