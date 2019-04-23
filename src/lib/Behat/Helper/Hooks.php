<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Testwork\Tester\Result\TestResult;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use WebDriver\LogType;

class Hooks extends RawMinkContext
{
    private const CONSOLE_LOGS_LIMIT = 10;

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

    /** @AfterStep */
    public function getBrowserLogAfterFailedStep(AfterStepScope $scope)
    {
        if ($scope->getTestResult()->getResultCode() !== TestResult::FAILED) {
            return;
        }

        $driver = $this->getSession()->getDriver();
        if ($driver instanceof Selenium2Driver) {
            $logEntries = $driver->getWebDriverSession()->log(LogType::BROWSER);

            if (empty($logEntries)) {
                return;
            }

            $this->print('JS console errors:');
            $counter = 0;
            foreach ($logEntries as $entry) {
                if ($counter >= self::CONSOLE_LOGS_LIMIT) {
                    return;
                }

                $this->print($entry['message']);
                ++$counter;
            }
        }
    }

    private function print(string $message): void
    {
        echo sprintf('%s%s', $message, PHP_EOL);
    }
}
