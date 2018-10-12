<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use EzSystems\EzPlatformPageBuilder\Tests\Behat\Environment\EnterpriseEnvironmentConstants;
use Tests\AppBundle\Behat\PlatformDemoEnvironmentConstants;
use Tests\AppBundle\Behat\EnterpriseDemoEnvironmentConstants;

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

    public static function getInstallType(): string
    {
        return self::$installType;
    }

    private static function getProperEnvironment(int $installType)
    {
        switch ($installType) {
            case InstallType::PLATFORM:
                return new PlatformEnvironmentConstants();
            case InstallType::PLATFORM_DEMO:
                return new PlatformDemoEnvironmentConstants();
            case InstallType::ENTERPRISE:
                return new EnterpriseEnvironmentConstants();
            case InstallType::ENTERPRISE_DEMO:
                return new EnterpriseDemoEnvironmentConstants();
            default:
                throw new \Exception('Unrecognised install type');
        }
    }
}
