<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\SystemInfoPage;

class SystemInfoContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Page\SystemInfoPage */
    private $systemInfoPage;

    public function __construct(SystemInfoPage $systemInfoPage)
    {
        $this->systemInfoPage = $systemInfoPage;
    }

    /**
     * @When I go to :tabName tab in System Information
     */
    public function iGoToTabInSystemInfo(string $tabName): void
    {
        $this->systemInfoPage->verifyIsLoaded();
        $this->systemInfoPage->goToTab($tabName);
    }

    /**
     * @Then I see :tabName system information table
     */
    public function iSeeSystemInformationTable(string $tabName): void
    {
        $this->systemInfoPage->verifyCurrentTableHeader($tabName);
    }

    /**
     * @Then I see listed packages
     */
    public function iSeeListedPackages(TableNode $packages): void
    {
        $packageNames = array_column($packages->getHash(), 'Name');
        $this->systemInfoPage->verifyPackages($packageNames);
    }

    /**
     * @Then I see listed bundles
     */
    public function iSeeListedBundles(TableNode $bundles): void
    {
        $bundleNames = array_column($bundles->getHash(), 'Name');
        $this->systemInfoPage->verifyBundles($bundleNames);
    }
}
