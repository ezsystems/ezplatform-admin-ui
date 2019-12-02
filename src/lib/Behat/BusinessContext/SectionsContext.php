<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\SectionPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\SectionsPage;
use PHPUnit\Framework\Assert;

class SectionsContext extends BusinessContext
{
    /**
     * @Then content items list in section :sectionName contains items
     */
    public function sectionContainsProperContentItems(string $sectionName, TableNode $contentItems): void
    {
        $sectionPage = PageObjectFactory::createPage($this->browserContext, SectionPage::PAGE_NAME, $sectionName);
        $hash = $contentItems->getHash();
        foreach ($hash as $contentItem) {
            $sectionPage->verifyContentItem($contentItem['Name'], $contentItem['Content Type'], $contentItem['Path']);
        }
    }

    /**
     * @Then Going to sections list we see there's empty :sectionName on list
     */
    public function goToSectionsAndVerifySectionIsEmpty(string $sectionName): void
    {
        $emptyContainerCellValue = '0';

        $upperMenu = ElementFactory::createElement($this->browserContext, UpperMenu::ELEMENT_NAME);
        $upperMenu->goToTab('Admin');
        $upperMenu->goToSubTab('Sections');

        $contentsCount = PageObjectFactory::createPage($this->browserContext, SectionsPage::PAGE_NAME)
            ->adminList->table->getTableCellValue($sectionName, 'Assigned content');

        if (($contentsCount !== $emptyContainerCellValue)) {
            Assert::fail(sprintf('There is no empty %s on the "Section" list.', $sectionName));
        }
    }
}
