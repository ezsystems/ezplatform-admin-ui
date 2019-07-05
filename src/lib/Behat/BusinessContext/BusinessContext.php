<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use EzSystems\Behat\Browser\Context\BrowserContext;

abstract class BusinessContext implements Context
{
    /** @var BrowserContext */
    protected $browserContext;

    /** @BeforeScenario
     * @param BeforeScenarioScope $scope Behat scope
     */
    public function getUtilityContext(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();
        $this->browserContext = $environment->getContext(BrowserContext::class);
    }
}
