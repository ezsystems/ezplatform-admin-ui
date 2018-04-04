<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\SystemInfoPage;

class SystemInfoContext extends BusinessContext
{
    private $systemInfoTableMapping = [
        'Bundles' => 'Symfony Kernel',
        'Packages' => 'Composer',
    ];

    /**
     * @When I go to :tabName tab in System Information
     */
    public function iGoToTabInSYstemInfo(string $tabName): void
    {
        $systemInfoPage = PageObjectFactory::createPage($this->utilityContext, SystemInfoPage::PAGE_NAME);
        $systemInfoPage->verifyIsLoaded();
        $systemInfoPage->navLinkTabs->goToTab($tabName);
    }

    /**
     * @Then I see :tabName system information table
     */
    public function iSeeSystemInformationTable(string $tabName): void
    {
        $systemInfoPage = PageObjectFactory::createPage($this->utilityContext, SystemInfoPage::PAGE_NAME);
        $systemInfoPage->verifySystemInfoTable($tabName);
    }

    /**
     * @Then I see :tableName table with given records
     */
    public function iSeeRecordsInSystemInformation(string $tableName, TableNode $records): void
    {
        $systemInfoPage = PageObjectFactory::createPage($this->utilityContext, SystemInfoPage::PAGE_NAME);
        $systemInfoPage->navLinkTabs->goToTab($this->systemInfoTableMapping[$tableName]);

        $hash = $records->getHash();
        $systemInfoPage->verifySystemInfoRecords($tableName, $hash);
    }
}
