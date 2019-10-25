<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;

/** Element that describes upper menu (Content, Admin, Page and theirs children) */
class UpperMenu extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Upper Menu';

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'menuButton' => '.ez-main-nav .nav-link',
            'submenuButton' => '.ez-main-sub-nav .nav-link',
            'dashboardLink' => '.navbar-brand',
            'pendingNotificationsCount' => '.ez-user-menu__avatar-wrapper.n-pending-notifications',
            'userSettingsToggle' => '.ez-user-menu__name-wrapper',
            'userSettingsItem' => '.ez-user-menu__item',
        ];
    }

    /**
     * Clicks on top menu, for example "Content" tab.
     *
     * @param $tabName
     */
    public function goToTab(string $tabName): void
    {
        $this->context->getElementByText($tabName, $this->fields['menuButton'])->click();
    }

    /**
     * Clicks on top menu dashboard link.
     *
     * @param $tabName
     */
    public function goToDashboard(): void
    {
        $this->context->findElement($this->fields['dashboardLink'])->click();
    }

    /**
     * Clicks on expanded submenu, for example "Content Structure" in "Content" section.
     *
     * @param $tabName
     */
    public function goToSubTab(string $tabName): void
    {
        $this->context->waitUntil(5, function () use ($tabName) {
            return $this->context->getElementByText($tabName, $this->fields['submenuButton']) !== null;
        });

        $this->context->getElementByText($tabName, $this->fields['submenuButton'])->click();
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['menuButton']);
    }

    public function getNotificationsCount(): int
    {
        return (int) $this->context->findElement($this->fields['pendingNotificationsCount'])->getAttribute('data-count');
    }

    public function chooseFromUserDropdown(string $option): void
    {
        $this->context->findElement($this->fields['userSettingsToggle'])->click();
        $this->context->getElementByText($option, $this->fields['userSettingsItem'])->click();
    }
}
