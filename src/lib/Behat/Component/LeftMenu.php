<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class LeftMenu extends Component
{
    public function clickButton(string $buttonName): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('buttonSelector'))->getByCriterion(new ElementTextCriterion($buttonName))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('menuSelector'))->assert()->isVisible();
    }

    public function browse(): void
    {
        $this->clickButton('Browse');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('buttonSelector', '.ez-sticky-container .btn'),
            new VisibleCSSLocator('menuSelector', '.ez-side-menu'),
        ];
    }
}
