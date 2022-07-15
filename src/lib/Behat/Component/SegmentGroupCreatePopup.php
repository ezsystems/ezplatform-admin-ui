<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Locator\XPathLocator;

class SegmentGroupCreatePopup extends Component
{
    public function fillFieldWithValue(string $fieldName, $value): void
    {
        $this->verifyIsLoaded();

        $field = $this->getField($fieldName);
        $fieldType = $field->getAttribute('type');

        $this->getHTMLPage()->setTimeout(3)->waitUntil(static function () use ($field, $fieldType, $value) {
            $field->setValue($value);

            return $fieldType !== 'text' || $value === $field->getValue();
        }, sprintf('Failed to set correct value in input field. Expected: %s. Actual: %s', $value, $field->getValue()));
    }

    private function getField(string $fieldName): ElementInterface
    {
        return $this->getHTMLPage()
            ->findAll(new XPathLocator('input', '//label/..'))
            ->getByCriterion(new ElementTextCriterion($fieldName))
            ->find(new VisibleCSSLocator('input', 'input'));
    }

    public function fillSegmentFieldWithValue(string $name, $identifier): void
    {
        $lastrow = $this->getHTMLPage()
            ->findAll(new VisibleCSSLocator('lastCell', '.ez-table--add-segments tbody tr'))->last();

        $nameInput = $lastrow->find(new VisibleCSSLocator('nameInput', ' [id*=name]'));
        $identifierInput = $lastrow->find(new VisibleCSSLocator('identifierInput', ' [id*=identifier]'));
        $nameInput->setValue($name);
        $identifierInput->setValue($identifier);
    }

    public function getFieldValue($label)
    {
        return $this->getField($label)->getValue();
    }

    public function confirmSegmentGroupCreation(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createSegmentButton'))->click();
    }

    public function addNewSegmentRow(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addSegmentButton'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(5)
            ->find($this->getLocator('createSegmentPopup'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createSegmentButton', '#segment_group_create_create'),
            new VisibleCSSLocator('createSegmentPopup', '#create-segment-group-modal > div > div'),
            new VisibleCSSLocator('addSegmentButton', 'div.ez-table-header__tools > button.btn.btn-icon.ez-btn.ez-btn--add'),
        ];
    }
}
