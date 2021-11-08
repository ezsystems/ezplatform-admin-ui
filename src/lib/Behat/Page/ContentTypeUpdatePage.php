<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class ContentTypeUpdatePage extends AdminUpdateItemPage
{
    public function fillFieldDefinitionFieldWithValue(string $fieldName, string $label, string $value)
    {
        $this->expandLastFieldDefinition();
        $this->getHTMLPage()->find($this->getLocator('fieldDefinitionOpenContainer'))
            ->findAll($this->getLocator('field'))->getByCriterion(new ElementTextCriterion($label))
            ->find($this->getLocator('fieldInput'))
            ->setValue($value);
    }

    public function expandLastFieldDefinition(): void
    {
        $fieldToggleLocator = $this->getLocator('fieldDefinitionToggle');
        $lastFieldDefinition = $this->getHTMLPage()
            ->findAll($fieldToggleLocator)
            ->last();
        $lastFieldDefinition->mouseOver();
        $lastFieldDefinition->click();
        $this->getHTMLPage()->setTimeout(5)
            ->waitUntilCondition(new ElementTransitionHasEndedCondition($this->getHTMLPage(), $fieldToggleLocator));
    }

    public function specifyLocators(): array
    {
        return array_merge(parent::specifyLocators(), [
            new VisibleCSSLocator('fieldDefinitionContainer', '.ibexa-collapse--field-definition  div.ibexa-collapse__header'),
            new VisibleCSSLocator('field', '.form-group'),
            new VisibleCSSLocator('contentTypeAddButton', '.ibexa-content-type-edit__add-field-definitions-group-btn'),
            new VisibleCSSLocator('contentTypeCategoryList', ' div.ibexa-content-type-edit__add-field-definitions-group > ul > li:nth-child(n):not(.ibexa-popup-menu__item-action--disabled)'),
            new VisibleCSSLocator('availableFieldLabelList', '.ibexa-available-field-types__list > li'),
            new VisibleCSSLocator('workspace', '#content_collapse > div.ibexa-collapse__body-content > div'),
            new VisibleCSSLocator('fieldDefinitionToggle', '.ibexa-collapse:nth-last-child(2) > div.ibexa-collapse__header > button:last-child:not([data-bs-target="#content_collapse"])'),
            new VisibleCSSLocator('fieldDefinitionOpenContainer', '[data-collapsed="false"] .ibexa-content-type-edit__field-definition-content'),
            new VisibleCSSLocator('selectBlocksDropdown', '.ez-page-select-items__toggler'),
        ]);
    }

    public function addFieldDefinition(string $fieldName)
    {
        $availableFieldLabel = $this->getLocator('availableFieldLabelList');
        $listElement = $this->getHTMLPage()
            ->findAll($availableFieldLabel)
            ->getByCriterion(new ElementTextCriterion($fieldName));
        $listElement->mouseOver();

        $fieldPosition = array_search(
            $fieldName,
            $this->getHTMLPage()->findAll($this->getLocator('availableFieldLabelList'))->mapBy(new ElementTextMapper()),
            true
        ) + 1; // CSS selectors are 1-indexed

        $availableFieldLabelsScript = "document.querySelector('.ibexa-available-field-types__list > li:nth-child(%d) > .ibexa-available-field-type__label')";
        $scriptToExecute = sprintf($availableFieldLabelsScript, $fieldPosition);
        $this->getSession()->executeScript($scriptToExecute);

        $workspace = sprintf('document.querySelector(\'%s\')', $this->getLocator('workspace')->getSelector());
        $this->getHTMLPage()->dragAndDrop($scriptToExecute, $workspace, $workspace);
        usleep(1500000); //TODO: add proper wait condition
    }

    public function clickAddButton(): void
    {
        $this->getHTMLPage()->find($this->getLocator('contentTypeAddButton'))->mouseOver();
        $this->getHTMLPage()->find($this->getLocator('contentTypeAddButton'))->click();
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(
                new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('contentTypeCategoryList'))
            );
        $this->getHTMLPage()->find($this->getLocator('contentTypeCategoryList'))->mouseOver();
    }

    public function selectContentTypeCategory(string $categoryName): void
    {
        $categoryLocator = $this->getLocator('contentTypeCategoryList');
        $listElement = $this->getHTMLPage()
            ->findAll($categoryLocator)
            ->getByCriterion(new ElementTextCriterion($categoryName));
        $listElement->mouseOver();
        $listElement->click();
    }

    public function expandDefaultBlocksOption(): void
    {
        $dropdownLocator = $this->getLocator('selectBlocksDropdown');
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(
                new ElementExistsCondition($this->getHTMLPage(), $dropdownLocator)
            );
        $this->getHTMLPage()
            ->findAll($dropdownLocator)->getByCriterion(new ElementTextCriterion('Select blocks'))->click();
        $this->getHTMLPage()
            ->findAll($dropdownLocator)->getByCriterion(new ElementTextCriterion('default'))->click();
    }

    public function selectBlock(string $blockName): void
    {
        $blockFindingScript = "document.querySelector('.ez-page-select-items__item .form-check .form-check-input[value=\'%s\']').click()";
        $scriptToExecute = sprintf($blockFindingScript, $blockName);
        $this->getSession()->executeScript($scriptToExecute);
    }

    public function verifyIsLoaded(): void
    {
        parent::verifyIsLoaded();
        $this->getHTMLPage()->find($this->getLocator('contentTypeAddButton'))->assert()->isVisible();
    }
}
