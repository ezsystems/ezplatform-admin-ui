<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

abstract class InstallType
{
    public const PLATFORM = 1;
    public const PLATFORM_DEMO = 2;
    public const ENTERPRISE = 3;
    public const ENTERPRISE_DEMO = 4;
}
