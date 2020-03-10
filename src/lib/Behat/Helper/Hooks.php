<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use Behat\Behat\Hook\Call\BeforeStep;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Testwork\Tester\Result\TestResult;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use Psr\Log\LoggerInterface;

class Hooks extends RawMinkContext
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
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
        $this->logger->error(sprintf('Behat: Starting Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @BeforeStep
     */
    public function logStartingStep(BeforeStepScope $scope)
    {
        $this->logger->error(sprintf('Behat: Starting Step "%s"', $scope->getStep()->getText()));
    }

    /** @AfterScenario
     */
    public function logEndingScenario(AfterScenarioScope $scope)
    {
        $this->logger->error(sprintf('Behat: Ending Scenario "%s"', $scope->getScenario()->getTitle()));
    }

    /** @AfterStep
     */
    public function logEndingStep(AfterStepScope $scope)
    {
        $this->logger->error(sprintf('Behat: Ending Step "%s"', $scope->getStep()->getText()));
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

        $testLogProvider = new TestLogProvider($this->getSession(), $this->getKernel()->getLogDir());

        $applicationsLogs = $testLogProvider->getApplicationLogs();
        $browserLogs = $testLogProvider->getBrowserLogs();

        echo $this->formatForDisplay($browserLogs, 'JS Console errors:');
        echo $this->formatForDisplay($applicationsLogs, 'Application logs:');
    }

    public function formatForDisplay(array $logEntries, string $sectionName)
    {
        $output = sprintf('%s' . PHP_EOL, $sectionName);

        if (empty($logEntries)) {
            $output .= sprintf("\t No logs for this section.") . PHP_EOL;
        }

        foreach ($logEntries as $logEntry) {
            $output .= sprintf("\t%s" . PHP_EOL, $logEntry);
        }

        return $output;
    }
}
