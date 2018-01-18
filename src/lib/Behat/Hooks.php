<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat;

use Behat\Behat\Context\Context;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class Hooks implements Context
{
    use KernelDictionary;

    /** Tag used to mark tests that require database restore before running. */
    private const RESTORE_ENVIRONMENT_TAG_BEFORE = 'restoreEnvironmentBefore';

    /** Tag used to mark tests that require database restore before running. */
    private const RESTORE_ENVIRONMENT_TAG_AFTER = 'restoreEnvironmentAfter';

    /** @BeforeScenario
     * Restores the database and clears cache for tests marked with restoreStudioDatabase tag
     *
     * @param BeforeScenarioScope $scope
     */
    public function restoreEnvironmentBeforeScenario(BeforeScenarioScope $scope)
    {
        if (!$scope->getScenario()->hasTag(self::RESTORE_ENVIRONMENT_TAG_BEFORE)) {
            return;
        }

        $envRestorer = new EnvironmentRestore($this->getContainer());

        $envRestorer->restoreDatabase();
        $envRestorer->clearCache();
    }

    /** @AfterScenario
     * Restores the database and clears cache for tests marked with restoreStudioDatabase tag
     *
     * @param AfterScenarioScope $scope
     */
    public function restoreEnvironmentAfterScenario(AfterScenarioScope $scope)
    {
        if (!$scope->getScenario()->hasTag(self::RESTORE_ENVIRONMENT_TAG_AFTER)) {
            return;
        }

        $envRestorer = new EnvironmentRestore($this->getContainer());

        $envRestorer->restoreDatabase();
        $envRestorer->clearCache();
    }
}