<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageObject\DashboardPage;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use PHPUnit\Framework\Assert;

class DashboardContext extends BusinessContext
{
    /**
     * @Then going to dashboard we see there's draft :draftName on list
     */
    public function goingToDashboardISeeDraft(string $draftName): void
    {
        Assert::assertTrue(
            $this->goToDashboardAndCheckIfDraftIsOnList($draftName),
            sprintf('There\'s no draft %s on list', $draftName)
        );
    }

    /**
     * @Then going to dashboard we see there's no draft :draftName on list
     */
    public function goingToDashboardISeeNoDraft(string $draftName): void
    {
        Assert::assertFalse(
            $this->goToDashboardAndCheckIfDraftIsOnList($draftName),
            sprintf('There\'s draft %s on list', $draftName)
        );
    }

    /**
     * @Given I start editing content draft :contentDraftName
     */
    public function startEditingContentDraft(string $contentDraftName): void
    {
        $dashboardPage = PageObjectFactory::createPage($this->browserContext, DashboardPage::PAGE_NAME);
        $dashboardPage->dashboardTable->clickEditButton($contentDraftName);
    }

    private function goToDashboardAndCheckIfDraftIsOnList(string $draftName): bool
    {
        $this->goToDashboard();

        return $this->isDraftOnList($draftName);
    }

    private function goToDashboard(): void
    {
        $dashboardPage = PageObjectFactory::createPage($this->browserContext, DashboardPage::PAGE_NAME);
        $dashboardPage->open();
    }

    private function isDraftOnList(string $draftName): bool
    {
        $dashboardPage = PageObjectFactory::createPage($this->browserContext, DashboardPage::PAGE_NAME);
        if ($dashboardPage->isListEmpty()) {
            return false;
        }

        return $dashboardPage->dashboardTable->isElementOnCurrentPage($draftName);
    }
}
