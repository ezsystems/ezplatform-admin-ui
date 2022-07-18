<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\SegmentGroupCreatePopup;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class SegmentsGroupPage extends Page
{
    /**
     * @var \Ibexa\AdminUi\Behat\Component\SegmentGroupCreatePopup
     */
    private $segmentGroupCreatePopup;

    public function __construct(Session $session, Router $router, SegmentGroupCreatePopup $segmentGroupCreatePopup)
    {
        parent::__construct($session, $router);
        $this->segmentGroupCreatePopup = $segmentGroupCreatePopup;
    }

    public function fillSegmentGroupFieldWithValue(string $fieldName, $value): void
    {
        $this->segmentGroupCreatePopup->fillFieldWithValue($fieldName, $value);
    }

    public function fillSegmentFieldWithValue(string $name, $identifier): void
    {
        $this->segmentGroupCreatePopup->fillSegmentFieldWithValue($name, $identifier);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createSegmentGroupButton', '.ez-icon--create'),
            new VisibleCSSLocator('fieldInput', 'input'),
            new VisibleCSSLocator('title', '.ez-header h1'),
            new VisibleCSSLocator('segmentGroupTrashButton', 'button#bulk-delete-segment-group'),
            new VisibleCSSLocator('segmentGroupDeleteButton', '.btn.btn-primary.btn--trigger'),
        ];
    }

    public function openSegmentGroupCreationWindow(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createSegmentGroupButton'))->click();
    }

    public function addNewSegmentRow(): void
    {
        $this->segmentGroupCreatePopup->addNewSegmentRow();
    }

    public function confirmNewSegmentGroupCreation(): void
    {
        $this->segmentGroupCreatePopup->confirmSegmentGroupCreation();
    }

    public function selectLastSegmentGroupCheckbox(): void
    {
        $lastSegmentGroupRow = $this->getHTMLPage()
            ->findAll(new VisibleCSSLocator('lastSegmentGroupRow', 'td input.ez-input--checkbox'))->last();
        $lastSegmentGroupRow->click();
    }

    public function openSegmentGroupDeletionConfirmationWindow(): void
    {
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('segmentGroupTrashButton')));
        $this->getHTMLPage()
            ->find($this->getLocator('segmentGroupTrashButton'))->click();
    }

    public function confirmSegmentDeletion(): void
    {
        $this->getHTMLPage()->find($this->getLocator('segmentGroupDeleteButton'))->click();
    }

    public function getName(): string
    {
        return 'Segmentation';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Segment Groups',
            $this->getHTMLPage()->find($this->getLocator('title'))->getText()
        );
    }

    protected function getRoute(): string
    {
        return 'segmentation/group/list';
    }
}
