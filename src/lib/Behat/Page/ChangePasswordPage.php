<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

class ChangePasswordPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\ContentActionsMenu */
    private $contentActionsMenu;

    public function __construct(Session $session, Router $router, ContentActionsMenu $contentActionsMenu)
    {
        parent::__construct($session, $router);
        $this->contentActionsMenu = $contentActionsMenu;
    }

    public function verifyIsLoaded(): void
    {
        $this->contentActionsMenu->verifyIsLoaded();
        $this->getHTMLPage()->find($this->getLocator('title'))->assert()->textEquals('Change my password');
    }

    public function setOldPassword(string $value): void
    {
        $this->getHTMLPage()->find($this->getLocator('oldPassword'))->setValue($value);
    }

    public function setNewPassword(string $value): void
    {
        $this->getHTMLPage()->find($this->getLocator('newPassword'))->setValue($value);
    }

    public function setConfirmPassword(string $value): void
    {
        $this->getHTMLPage()->find($this->getLocator('confirmPassword'))->setValue($value);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('oldPassword', '#user_password_change_oldPassword'),
            new VisibleCSSLocator('newPassword', '#user_password_change_newPassword_first'),
            new VisibleCSSLocator('confirmPassword', '#user_password_change_newPassword_second'),
            new VisibleCSSLocator('title', '.ez-page-title h1'),
        ];
    }

    protected function getRoute(): string
    {
        return '/user/change-password';
    }

    public function getName(): string
    {
        return 'Change password page';
    }
}
