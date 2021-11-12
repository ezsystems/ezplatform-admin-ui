<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Ibexa\Behat\Browser\Page\LoginPage;
use PHPUnit\Framework\Assert;
use Psr\Log\LoggerInterface;

class TestContext implements Context
{
    /** @var int */
    private $result;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var LoginPage
     */
    private $loginPage;

    public function __construct(LoggerInterface $logger, LoginPage $loginPage)
    {
        $this->result = 0;
        $this->logger = $logger;
        $this->loginPage = $loginPage;
    }

    /**
     * @Given I start with number :number
     */
    public function setResult(string $number): void
    {
        $this->result = (int) $number;
        $this->logger->critical('TEST CRITICAL');
        $this->loginPage->open('admin');
        $this->loginPage->loginSuccessfully('admin', 'publish');
    }

    /**
     * @Given I add number :number
     */
    public function addNumber(string $number): void
    {
        $this->result += (int) $number;
    }

    /**
     * @Given the result should be :expectedNumber
     */
    public function verifyResult(string $expectedNumber): void
    {
        Assert::assertEquals((int) $expectedNumber, $this->result);
    }
}