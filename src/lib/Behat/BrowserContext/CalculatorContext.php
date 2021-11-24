<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use EzSystems\EzPlatformAdminUi\Behat\BrowserContext\BasketLogic;
use EzSystems\EzPlatformAdminUi\Behat\Page\MyDashboardPage;
use Ibexa\Behat\Browser\Page\LoginPage;
use PHPUnit\Framework\Assert;

class CalculatorContext implements Context
{
    private $basketLogic;

    /**
     * @var \Ibexa\Behat\Browser\Page\LoginPage
     */
    private $loginPage;
    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\Page\MyDashboardPage
     */
    private $dashboardPage;

    public function __construct(BasketLogic $basketLogic, LoginPage $loginPage, MyDashboardPage $dashboardPage)
    {
        $this->basketLogic = $basketLogic;
        $this->loginPage = $loginPage;
        $this->dashboardPage = $dashboardPage;
    }

    /**
     * @Given my basket is empty
     */
    public function basketIsEmpty(): void
    {
        $this->basketLogic->empty();
    }

    /**
     * @When I add product with price :productPrice
     */
    public function addProductToBasket(string $productPrice): void
    {
        $this->basketLogic->addProduct((int) $productPrice);
    }

    /**
     * @Then total sum should be equal to :expectedAmount
     */
    public function verifyTotalAmountEquals(string $expectedAmount): void
    {
        Assert::assertEquals($expectedAmount, $this->basketLogic->getTotalValue());
    }

    /**
     * @Given I open the Login Page test
     */
    public function test(): void
    {
        $this->loginPage->open('admin');
        $this->loginPage->loginSuccessfully('admin', 'publish');
        $this->dashboardPage->verifyIsLoaded();
    }
}
