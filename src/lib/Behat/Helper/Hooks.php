<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use Behat\Behat\Hook\Call\BeforeStep;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Testwork\Tester\Result\TestResult;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use Psr\Log\LoggerInterface;
use WebDriver\LogType;

class Hooks extends RawMinkContext
{
    private const CONSOLE_LOGS_LIMIT = 10;
    private const APPLICATION_LOGS_LIMIT = 25;
    private const LOG_FILE_NAME = 'travis_test.log';
    private $logger;

    use KernelDictionary;

    /**
     * @injectService $logger @logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /** @BeforeScenario
     */
    public function logStartingScenario(BeforeScenarioScope $scope)
    {
        $this->logger->error(sprintf('Starting Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @BeforeStep
     */
    public function logStartingStep(BeforeStepScope $scope)
    {
        $this->logger->error(sprintf('Starting Step %s', $scope->getStep()->getText()));
    }

    /** @AfterScenario
     */
    public function logEndingScenario(AfterScenarioScope $scope)
    {
        $this->logger->error(sprintf('Ending Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @AfterStep
     */
    public function logEndingStep(AfterStepScope $scope)
    {
        $this->logger->error(sprintf('Ending Step %s', $scope->getStep()->getText()));
    }

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

    /** @AfterStep */
    public function getLogsAfterFailedStep(AfterStepScope $scope)
    {
        if ($scope->getTestResult()->getResultCode() !== TestResult::FAILED) {
            return;
        }

        $driver = $this->getSession()->getDriver();
        if ($driver instanceof Selenium2Driver) {
            $browserLogEntries = $this->parseBrowserLogs($driver->getWebDriverSession()->log(LogType::BROWSER));
            $this->displayLogEntries('JS console errors:', $browserLogEntries);
        }

        $logReader = new LogFileReader();

        $applicationLogEntries = $logReader->getLastLines(sprintf('%s/%s', $this->getKernel()->getLogDir(), self::LOG_FILE_NAME), self::APPLICATION_LOGS_LIMIT);
        $this->displayLogEntries('Application errors:', $applicationLogEntries);
    }

    private function parseBrowserLogs($logEntries): array
    {
        $filter = new BrowserLogFilter();

        if (empty($logEntries)) {
            return [];
        }

        $errorMessages = array_column($logEntries, 'message');
        $errorMessages = $filter->filter($errorMessages);

        return \array_slice($errorMessages, 0, self::CONSOLE_LOGS_LIMIT);
    }

    private function displayLogEntries($sectionName, $logEntries)
    {
        if (empty($logEntries)) {
            return;
        }

        echo sprintf('%s' . PHP_EOL, $sectionName);

        foreach ($logEntries as $logEntry) {
            echo sprintf('%s' . PHP_EOL, $logEntry);
        }
    }
}
