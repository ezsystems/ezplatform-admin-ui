<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class IbexaDropdown extends Component
{
    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(2)
            ->waitUntilCondition(new ElementTransitionHasEndedCondition($this->getHTMLPage(), $this->getLocator('isIbexaDropdownVisible')));
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('ibexaDropdownExtended', '.ibexa-dropdown-popover .ibexa-dropdown__items .ibexa-dropdown__item'),
            new VisibleCSSLocator('ibexaDropdownLabel', '.ibexa-dropdown__item-label'),
            new VisibleCSSLocator('isIbexaDropdownVisible', '.ibexa-dropdown-popover'),
        ];
    }

    public function selectOption(string $value)
    {
        $this->verifyIsLoaded();
        $dropdownOptionLocator = $this->getLocator('ibexaDropdownExtended');
        $listElement = $this->getHTMLPage()
            ->findAll($dropdownOptionLocator)
            ->getByCriterion(new ElementTextCriterion($value));
        usleep(2000000);
        $listElement->mouseOver();
        usleep(2000000);
        $listElement->find($this->getLocator('ibexaDropdownLabel'))->click();
    }
}
