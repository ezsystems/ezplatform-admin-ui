<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;

interface SiteAccessNameGeneratorInterface
{
    public function generate(SiteAccess $siteAccessIdentifier): string;
}
