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

class UpperMenu extends Component
{
    public function goToDashboard(): void
    {
        $this->getHTMLPage()->find($this->getLocator('dashboardLink'))->click();
    }

    public function hasUnreadNotification(): bool
    {
        return $this->getHTMLPage()
            ->setTimeout(5)
            ->findAll($this->getLocator('pendingNotification'))
            ->any();
    }

    public function openNotifications(): void
    {
        $this->getHTMLPage()->find($this->getLocator('userImage'))->click();
    }

    public function chooseFromUserDropdown(string $option): void
    {
        $this->getHTMLPage()->find($this->getLocator('userSettingsToggle'))->click();
        $this->getHTMLPage()->findAll($this->getLocator('userSettingsItem'))->getByCriterion(new ElementTextCriterion($option))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('userSettingsToggle'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('dashboardLink', '.ibexa-main-header__brand'),
            new VisibleCSSLocator('pendingNotification', '.ibexa-header-user-menu__notice-dot'),
            new VisibleCSSLocator('userSettingsToggle', '.ibexa-header-user-menu'),
            new VisibleCSSLocator('userImage', '.ibexa-header-user-menu__image'),
            new VisibleCSSLocator('userSettingsItem', '.ibexa-popup-menu__item'),
        ];
    }
}
