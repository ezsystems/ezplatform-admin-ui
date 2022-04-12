<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\RightMenu;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

class UserSettingsPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\RightMenu */
    private $rightMenu;

    public function __construct(Session $session, Router $router, RightMenu $rightMenu)
    {
        parent::__construct($session, $router);
        $this->rightMenu = $rightMenu;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('title'))->assert()->textEquals('User Settings');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('title', '.ez-page-title__content-name'),
            new VisibleCSSLocator('autosaveDraftEditButton', 'a[href$="autosave"]'),
            new VisibleCSSLocator('autosaveDraftValueDropdown', '#user_setting_update_value'),
            new VisibleCSSLocator('autosaveIntervalEdit', 'a[href$="interval"]'),
        ];
    }

    public function openAutosaveDraftEditionPage(): void
    {
        $this->getHTMLPage()->find($this->getLocator('autosaveDraftEditButton'))->click();
    }

    public function openAutosaveDraftIntervalEditionPage(): void
    {
        $this->getHTMLPage()->find($this->getLocator('autosaveIntervalEdit'))->click();
    }

    public function disableAutosave(): void
    {
        $this->rightMenu->verifyIsLoaded();
        $this->getHTMLPage()->find($this->getLocator('autosaveDraftValueDropdown'))->selectOption('disabled');
    }

    public function getName(): string
    {
        return 'User Settings';
    }

    protected function getRoute(): string
    {
        return '/user/settings/list';
    }
}
