<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

class SiteAccessKeyGenerator implements SiteAccessKeyGeneratorInterface
{
    public function generate(string $siteAccessIdentifier): string
    {
        return sprintf('%u', crc32($siteAccessIdentifier));
    }
}
