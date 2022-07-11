<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Locator\XPathLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class SegmentsGroupPage extends Page
{
    public function __construct(Session $session, Router $router)
    {
        parent::__construct($session, $router);
    }

    public function fillFieldWithValue(string $fieldName, $value): void
    {
        $field = $this->getField($fieldName);
        $fieldType = $field->getAttribute('type');

        $this->getHTMLPage()->setTimeout(3)->waitUntil(static function () use ($field, $fieldType, $value) {
            $field->setValue($value);

            return $fieldType !== 'text' || $value === $field->getValue();
        }, sprintf('Failed to set correct value in input field. Expected: %s. Actual: %s', $value, $field->getValue()));
    }

    public function fillSegmentFieldWithValue(string $name, $identifier): void
    {
        $lastrow = $this->getHTMLPage()
            ->findAll(new VisibleCSSLocator('lastCell', '.ez-table--add-segments tbody tr'))->last();

        $nameInput = $lastrow->find(new VisibleCSSLocator('nameInput', ' [id*=name]'));
        $identifierInput = $lastrow->find(new VisibleCSSLocator('identifierInput', ' [id*=identifier]'));
        $nameInput->setValue($name);
        $identifierInput->setValue($identifier);


//        $this->getHTMLPage()->find($this->getLocator($nameInput))->setValue($name);
//        $this->getHTMLPage()->find($this->getHTMLPage()->find($identifierInput))->setValue($identifier);

//
//        $fieldType = $field->getAttribute('type');
//
//        $this->getHTMLPage()->setTimeout(3)->waitUntil(static function () use ($field, $fieldType, $value) {
//            $field->setValue($value);
//
//            return $fieldType !== 'text' || $value === $field->getValue();
//        }, sprintf('Failed to set correct value in input field. Expected: %s. Actual: %s', $value, $field->getValue()));
    }

    public function getFieldValue($label)
    {
        return $this->getField($label)->getValue();
    }

    private function getField(string $fieldName): ElementInterface
    {
        return $this->getHTMLPage()
            ->findAll(new XPathLocator('input', '//label/..'))
            ->getByCriterion(new ElementTextCriterion($fieldName))
            ->find(new VisibleCSSLocator('input', 'input'));
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createSegmentGroupButton', '.ez-icon--create'),
            new VisibleCSSLocator('createSegmentPopup', '#create-segment-group-modal > div > div'),
            new VisibleCSSLocator('fieldInput', 'input'),
            new VisibleCSSLocator('createSegmentButton', '#segment_group_create_create'),
            new VisibleCSSLocator('title', '.ez-header h1'),
            new VisibleCSSLocator('addSegmentButton', 'div.ez-table-header__tools > button.btn.btn-icon.ez-btn.ez-btn--add'),
        ];
    }

    public function openSegmentGroupCreationWindow(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createSegmentGroupButton'))->click();
    }

    public function clickOnAddSegmentButton(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addSegmentButton'))->click();
    }

    public function clickOnCreateButton(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createSegmentButton'))->click();
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

    public function verifyComponentIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('createSegmentPopup'))->assert()->isVisible();
    }

    protected function getRoute(): string
    {
        return 'segmentation/group/list';
    }
}
