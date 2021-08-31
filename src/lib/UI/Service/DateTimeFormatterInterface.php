<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Service;

use DateTimeInterface;

interface DateTimeFormatterInterface
{
    public function formatDiff(DateTimeInterface $from, DateTimeInterface $to): string;
}
