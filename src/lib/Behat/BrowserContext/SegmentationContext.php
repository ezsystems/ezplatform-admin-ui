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

class SegmentationContext implements Context
{
    /**
     * @var \Ibexa\AdminUi\Behat\Page\SegmentsGroupPage
     */
    private $segmentsGroupPage;
    /**
     * @var \Ibexa\AdminUi\Behat\Page\SegmentsEditPage
     */
    private $segmentsEditPage;

    public function __construct(SegmentsGroupPage $segmentsGroupPage, SegmentsEditPage $segmentsEditPage)
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
        $this->segmentsGroupPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->segmentsGroupPage->fillSegmentGroupFieldWithValue($row['label'], $row['value']);
        }
    }

    /**
     * @When I add segment with :name name and :identifier identifier to segment group during segment group creation
     */
    public function iAddSegmentToSegmentGroupDuringCreation(string $name, string $identifier): void
    {
        $this->segmentsGroupPage->addNewSegmentRow();
        $this->segmentsGroupPage->fillSegmentFieldWithValue($name, $identifier);
    }

    /**
     * @When I add segment with :name name and :identifier identifier to segment group during segment group edition
     */
    public function iAddSegmentToSegmentGroupDuringEdition(string $name, string $identifier): void
    {
        $this->segmentsEditPage->openSegmentCreationWindow();
        $this->segmentsEditPage->fillSegmentFieldWithValue($name, $identifier);
        $this->segmentsEditPage->confirmNewSegmentAddition();
    }

    /**
     * @When I confirm creation of new segment group
     */
    public function iConfirmNewSegmentGroupCreation(): void
    {
        $this->segmentsGroupPage->confirmNewSegmentGroupCreation();
    }

    /**
     * @When There's segment group with :segmentGroupName name and :segmentGroupIdentifier identifier in Segment Group Information section
     */
    public function iVerifySegmentGroupInformationSection(string $segmentGroupName, string $segmentGroupIdentifier): void
    {
        $this->segmentsEditPage->verifyIsLoaded();
        $this->segmentsEditPage->verifySegmentGroupNameInEditPage($segmentGroupName);
        $this->segmentsEditPage->verifySegmentGroupIdentifierInEditPage($segmentGroupIdentifier);
    }

    /**
     * @When There's segment with :segmentName name and :segmentIdentifier identifier in Segments Under Segment Group section
     */
    public function iVerifyAddedSegmentInformationSection(string $segmentName, string $segmentIdentifier): void
    {
        $this->segmentsEditPage->verifySegmentNameInEditPage($segmentName);
        $this->segmentsEditPage->verifySegmentIdentifierInEditPage($segmentIdentifier);
    }

    /**
     * @When I delete segment from Segments group
     */
    public function iDeleteSegment(): void
    {
        $this->segmentsEditPage->verifyIsLoaded();
        $this->segmentsEditPage->selectLastSegmentCheckbox();
        $this->segmentsEditPage->openSegmentDeletionConfirmationWindow();
        $this->segmentsEditPage->confirmSegmentDeletion();
    }

    /**
     * @When I delete segment group
     */
    public function iDeleteSegmentGroup(): void
    {
        $this->segmentsGroupPage->selectLastSegmentGroupCheckbox();
        $this->segmentsGroupPage->openSegmentGroupDeletionConfirmationWindow();
        $this->segmentsGroupPage->confirmSegmentDeletion();
    }
}
