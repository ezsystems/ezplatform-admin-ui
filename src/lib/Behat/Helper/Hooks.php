<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;

class Hooks extends RawMinkContext
{
    use KernelDictionary;

    /** @BeforeScenario
     */
    public function setInstallTypeBeforeScenario()
    {
        $env = new Environment($this->getContainer());
        $installType = $env->getInstallType();

        PageObjectFactory::setInstallType($installType);
        ElementFactory::setInstallType($installType);
        EzEnvironmentConstants::setInstallType($installType);
    }

    /** @BeforeScenario @restoreEnvironmentBefore
     * Restores the database and clears cache for tests marked with @restoreEnvironmentBefore tag
     */
    public function restoreEnvironmentBeforeScenario()
    {
        $env = new Environment($this->getContainer());

        $env->restoreDatabase();
        $env->clearCache();
    }

    /** @AfterScenario @restoreEnvironmentAfter
     * Restores the database and clears cache for tests marked with @restoreEnvironmentAfter tag
     */
    public function restoreEnvironmentAfterScenario()
    {
        $env = new Environment($this->getContainer());

        $env->restoreDatabase();
        $env->clearCache();
    }
}
