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
use PHPUnit\Framework\Assert;

/** Element that describes upper menu (Content, Admin, Page and theirs children) */
class UpperMenu extends Component
{
    public function goToTab(string $tabName): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('menuButton'))->getByCriterion(new ElementTextCriterion($tabName))->click();
    }

    public function goToDashboard(): void
    {
        $this->getHTMLPage()->find($this->getLocator('dashboardLink'))->click();
    }

    public function goToSubTab(string $tabName): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('submenuButton'))->getByCriterion(new ElementTextCriterion($tabName))->click();
    }

    public function getNotificationsCount(): int
    {
        return (int) $this->getHTMLPage()
            ->setTimeout(5)
            ->find($this->getLocator('pendingNotificationsCount'))
            ->getAttribute('data-count');
    }

    public function chooseFromUserDropdown(string $option): void
    {
        $this->getHTMLPage()->find($this->getLocator('userSettingsToggle'))->click();
        $this->getHTMLPage()->findAll($this->getLocator('userSettingsItem'))->getByCriterion(new ElementTextCriterion($option))->click();
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('menuButton'))->isVisible());
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('menuButton', '.ibexa-main-menu__navbar--first-level .ibexa-main-menu__item-action'),
            new VisibleCSSLocator('submenuButton', '.ibexa-main-menu__navbar--second-level .ibexa-main-menu__item-action'),
            new VisibleCSSLocator('dashboardLink', '.navbar-brand'),
            new VisibleCSSLocator('pendingNotificationsCount', '.ibexa-header-user-menu .n-pending-notifications'),
            new VisibleCSSLocator('userSettingsToggle', '.ibexa-header-user-menu'),
            new VisibleCSSLocator('userSettingsItem', '.ibexa-popup-menu__item'),
        ];
    }
}
