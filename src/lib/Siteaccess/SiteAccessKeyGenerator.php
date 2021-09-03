<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Siteaccess;

final class SiteAccessKeyGenerator implements SiteAccessKeyGeneratorInterface
{
    public function generate(string $siteAccessIdentifier): string
    {
        return sprintf('%u', crc32($siteAccessIdentifier));
    }
}

class_alias(SiteAccessKeyGenerator::class, 'EzSystems\EzPlatformAdminUi\Siteaccess\SiteAccessKeyGenerator');
