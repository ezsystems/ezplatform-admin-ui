<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\SegmentsEditPage;
use Ibexa\AdminUi\Behat\Page\SegmentsGroupPage;
use PHPUnit\Framework\Assert;

class SegmentationContext implements Context
{
    /**
     * @var SegmentsGroupPage
     */
    private $segmentsGroupPage;
    /**
     * @var SegmentsEditPage
     */
    private $segmentsEditPage;

    public function __construct (SegmentsGroupPage $segmentsGroupPage, SegmentsEditPage $segmentsEditPage)
    {
        $this->segmentsGroupPage = $segmentsGroupPage;
        $this->segmentsEditPage = $segmentsEditPage;
    }

    /**
     * @When I click on segment group creation popup button
     */
    public function iClickOnSegmentGroupCreationButton(): void
    {
        $this->segmentsGroupPage->openSegmentGroupCreationWindow();
    }

    /**
     * @When I fill segment group configuration fields
     */
    public function iFillSegmentGroupConfigurationFields(TableNode $table): void
    {
        $this->segmentsGroupPage->verifyComponentIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->segmentsGroupPage->fillFieldWithValue($row['label'], $row['value']);
        }
    }

    /**
     * @When I add segment to segment group
     */
    public function iAddSegmentToSegmentGroup(): void
    {

    }

    /**
     * @When I confirm creation of new segment group
     */
    public function iConfirmNewSegmentGroupCreation(): void
    {
        $this->segmentsGroupPage->clickOnCreateButton();
    }

    /**
     * @When There's segment group with :name name and :identifier identifier in Segment Group Information section
     */
    public function iAssertSegmentGroupInformationSection(): void
    {
usleep(23232323);
//        Assert::assertFalse($this->rolesPage->isRoleOnTheList($roleName));
  //      Assert::assertFalse($this->rolesPage->isRoleOnTheList($roleName));
    }

    /**
     * @When There's segment with "testsegid" name  and "testsegid" identifier in Segments Under Segment Group section
     */
    public function iAssertAddedSegmentInformationSection(): void
    {

    }


}
