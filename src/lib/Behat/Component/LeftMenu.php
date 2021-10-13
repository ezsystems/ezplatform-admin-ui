<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementAttributeCriterion;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\Criterion\LogicalOrCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class LeftMenu extends Component
{
    public function goToTab(string $tabName): void
    {
        $buttonCriteron = new LogicalOrCriterion([
            new ElementAttributeCriterion('data-bs-original-title', $tabName),
            new ElementTextCriterion($tabName),
        ]);

        $menuButton = $this->getHTMLPage()
            ->findAll($this->getLocator('menuItem'))
            ->getByCriterion($buttonCriteron);
        $menuButton->click();
        $menuButton->find(new VisibleCSSLocator('activeMarker', '.ibexa-main-menu__item-action.active'))->assert()->isVisible();
    }

    public function goToSubTab(string $tabName): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntil(function () {
                return $this->getMenuWidth() < 100;
            }, sprintf('Left menu did not collapse in time. Current width: %d', $this->getMenuWidth()));

        $this->getHTMLPage()
            ->findAll($this->getLocator('expandedMenuItem'))
            ->getByCriterion(new ElementTextCriterion($tabName))
            ->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('menuSelector'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('menuItem', '.ibexa-main-menu__navbar--first-level .ibexa-main-menu__item'),
            new VisibleCSSLocator('expandedMenuItem', '.ibexa-main-menu__item-action--second-level .ibexa-main-menu__item-text-column'),
            new VisibleCSSLocator('menuSelector', '.ibexa-main-menu'),
        ];
    }

    private function getMenuWidth(): int
    {
        return (int) $this->getHTMLPage()->executeJavaScript("return document.querySelector('.ibexa-main-menu__navbar--collapsed').clientWidth.toString()");
    }
}
