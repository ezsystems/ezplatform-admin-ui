<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

class Hooks extends RawMinkContext
{
    use KernelDictionary;

    /** @BeforeScenario
     */
    public function restartSessionBeforeScenario()
    {
        $this->getSession()->restart();
    }

    /** @BeforeScenario @restoreEnvironmentBefore
     * Restores the database and clears cache for tests marked with @restoreEnvironmentBefore tag
     */
    public function restoreEnvironmentBeforeScenario()
    {
        $envRestorer = new EnvironmentRestore($this->getContainer());

        $envRestorer->restoreDatabase();
        $envRestorer->clearCache();
    }

    /** @AfterScenario @restoreEnvironmentAfter
     * Restores the database and clears cache for tests marked with @restoreEnvironmentAfter tag
     */
    public function restoreEnvironmentAfterScenario()
    {
        $envRestorer = new EnvironmentRestore($this->getContainer());

        $envRestorer->restoreDatabase();
        $envRestorer->clearCache();
    }
}
