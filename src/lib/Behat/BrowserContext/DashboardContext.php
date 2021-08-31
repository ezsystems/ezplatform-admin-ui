<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Ibexa\AdminUi\Behat\Component\UpperMenu;
use Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage;
use Ibexa\AdminUi\Behat\Page\DashboardPage;
use PHPUnit\Framework\Assert;

class DashboardContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Component\UpperMenu */
    private $upperMenu;

    /** @var \Ibexa\AdminUi\Behat\Page\DashboardPage */
    private $dashboardPage;

    /** @var \Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage */
    private $contentUpdateItemPage;

    public function __construct(UpperMenu $upperMenu, DashboardPage $dashboardPage, ContentUpdateItemPage $contentUpdateItemPage)
    {
        $this->upperMenu = $upperMenu;
        $this->dashboardPage = $dashboardPage;
        $this->contentUpdateItemPage = $contentUpdateItemPage;
    }

    /**
     * @Given I go to dashboard
     */
    public function iGoToDashboard(): void
    {
        $this->upperMenu->goToDashboard();
    }

    /**
     * @Then there's draft :draftName on Dashboard list
     */
    public function goingToDashboardISeeDraft(string $draftName): void
    {
        Assert::assertTrue($this->dashboardPage->isDraftOnList($draftName));
    }

    /**
     * @Then there's no draft :draftName on Dashboard list
     */
    public function goingToDashboardISeeNoDraft(string $draftName): void
    {
        Assert::assertFalse($this->dashboardPage->isDraftOnList($draftName));
    }

    /**
     * @Given I start editing content draft :contentDraftName
     */
    public function startEditingContentDraft(string $contentDraftName): void
    {
        $this->dashboardPage->editDraft($contentDraftName);
        $this->contentUpdateItemPage->setExpectedPageTitle($contentDraftName);
        $this->contentUpdateItemPage->verifyIsLoaded();
    }
}
