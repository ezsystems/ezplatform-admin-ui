<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\InstallType;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformPageBuilder\Tests\Behat\PageObject\EnterprisePageObjectFactory;
use Tests\AppBundle\Behat\PageObject\DemoEnterprisePageObjectFactory;

class PageObjectFactory
{
    private static $installType;

    private static $factory;

    /**
     * Creates a Page object based on given Page Name.
     *
     * @param UtilityContext $context
     * @param string $pageName Name of the Page to creator
     * @param null[]|string[] $parameters additional parameters
     */
    public static function createPage(UtilityContext $context, string $pageName, ?string ...$parameters)
    {
        /* Note: no return type to enable type-hinting */

        if (self::$factory === null) {
            self::$factory = self::getFactory(self::$installType);
        }

        return self::$factory::createPage($context, $pageName, ...$parameters);
    }

    public static function setInstallType(int $installType)
    {
        self::$installType = $installType;
    }

    public static function getPreviewType(string $contentType)
    {
        /* Note: no return type to enable type-hinting */
        $factory = self::getFactory(self::$installType);

        return $factory::getPreviewType($contentType);
    }

    /**
     * @param int $installType
     *
     * @return PlatformPageObjectFactory|EnterprisePageObjectFactory
     *
     * @throws \Exception
     */
    private static function getFactory(int $installType): PlatformPageObjectFactory
    {
        switch ($installType) {
            case InstallType::PLATFORM:
            case InstallType::PLATFORM_DEMO:
                return new PlatformPageObjectFactory();
            case InstallType::ENTERPRISE:
                return new EnterprisePageObjectFactory();
            case InstallType::ENTERPRISE_DEMO:
                return new DemoEnterprisePageObjectFactory();
            default:
                throw new \Exception('Unrecognised install type');
        }
    }
}
