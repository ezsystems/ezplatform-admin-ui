<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class TestContext implements Context
{
    /** @var int */
    private $result;

    public function __construct()
    {
        $this->result = 0;
    }

    /**
     * @Given I start with number :number
     */
    public function setResult(string $number): void
    {
        $this->result = (int) $number;
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