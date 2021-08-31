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
            new VisibleCSSLocator('menuButton', '.ez-main-nav .nav-link'),
            new VisibleCSSLocator('submenuButton', '.ez-main-sub-nav .nav-link'),
            new VisibleCSSLocator('dashboardLink', '.navbar-brand'),
            new VisibleCSSLocator('pendingNotificationsCount', '.ez-user-menu__name-wrapper .n-pending-notifications'),
            new VisibleCSSLocator('userSettingsToggle', '.ez-user-menu__name-wrapper'),
            new VisibleCSSLocator('userSettingsItem', '.ez-user-menu__item'),
        ];
    }
}
