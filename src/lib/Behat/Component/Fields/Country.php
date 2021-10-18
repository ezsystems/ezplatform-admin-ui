<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class Country extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $this->getHTMLPage()->find($this->getLocator('dropdownSelector'))->click();
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('dropdownExpanded'))->isVisible());
        $this->getHTMLPage()->findAll($this->getLocator('dropdownItem'))->getByCriterion(new ElementTextCriterion($parameters['value']))->click();
        $this->getHTMLPage()->find($this->getLocator('dropdownSelector'))->click();
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezcountry';
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', 'select'),
            new VisibleCSSLocator('dropdownSelector', '.ibexa-dropdown__selection-info'),
            new VisibleCSSLocator('dropdownExpanded', '.ibexa-dropdown-popover .ibexa-dropdown__items'),
            new VisibleCSSLocator('dropdownItem', '.ibexa-dropdown__item'),
        ];
    }
}
