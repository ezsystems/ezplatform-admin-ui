<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use EzSystems\EzPlatformPageBuilder\Tests\Behat\Environment\EnterpriseEnvironmentConstants;

class EzEnvironmentConstants
{
    private static $installType;

    public static function setInstallType(int $installType)
    {
        self::$installType = $installType;
    }

    public static function get(string $key): string
    {
        $env = self::getProperEnvironment(self::$installType);

        return $env->values[$key];
    }

    private static function getProperEnvironment(int $installType)
    {
        switch ($installType) {
            case InstallType::PLATFORM:
                return new PlatformEnvironmentConstants();
            case InstallType::ENTERPRISE:
                return new EnterpriseEnvironmentConstants();
            default:
                throw new \Exception('Unrecognised install type');
        }
    }
}
