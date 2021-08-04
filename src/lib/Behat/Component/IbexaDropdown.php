<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class IbexaDropdown extends Component
{
    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('isIbexaDropdownVisible'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('ibexaDropdownExtendedWithChildren', 'div.ibexa-dropdown__wrapper > ul.ibexa-dropdown__items > li:nth-child(n) > ul >li:nth-child(n)'),
            new VisibleCSSLocator('ibexaDropdownExtended', '.ibexa-dropdown--is-expanded .ibexa-dropdown__items .ibexa-dropdown__item'),
            new VisibleCSSLocator('isIbexaDropdownVisible', '.ibexa-dropdown--is-expanded '),
        ];
    }

    public function selectOption(string $value)
    {
        $this->verifyIsLoaded();
        $dropdownOptionLocator = $this->getLocator('ibexaDropdownExtended');
        $this->getHTMLPage()
            ->findAll($dropdownOptionLocator)
            ->getByCriterion(new ElementTextCriterion($value))
            ->click();
    }
}
