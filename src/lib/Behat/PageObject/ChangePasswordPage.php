<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use PHPUnit\Framework\Assert;

class ChangePasswordPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Change my password';

    /**
     * @var RightMenu
     */
    private $rightMenu;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->rightMenu = ElementFactory::createElement($this->context, RightMenu::ELEMENT_NAME);
        $this->siteAccess = 'admin';
        $this->route = '/user/change-password';
        $this->pageTitleLocator = '.ez-header h1';
        $this->fields = [
            'oldPassword' => '#user_password_change_oldPassword',
            'newPassword' => '#user_password_change_newPassword_first',
            'confirmPassword' => '#user_password_change_newPassword_second',
        ];
    }

    public function verifyElements(): void
    {
        $this->rightMenu->verifyVisibility();
    }

    public function verifyRoute(): void
    {
        $expectedRoute = '/' . $this->siteAccess . $this->route;
        Assert::assertContains($expectedRoute, $this->getCurrentRoute());
    }

    public function verifyTitle(): void
    {
        Assert::assertContains($this::PAGE_NAME, $this->getPageTitle());
    }

    public function verifyIsLoaded(): void
    {
        $this->verifyElements();
        $this->verifyRoute();
        $this->verifyTitle();
    }

    public function setOldPassword(string $value): void
    {
        $this->context->findElement($this->fields['oldPassword'])->setValue($value);
    }

    public function setNewPassword(string $value): void
    {
        $this->context->findElement($this->fields['newPassword'])->setValue($value);
    }

    public function setConfirmPassword(string $value): void
    {
        $this->context->findElement($this->fields['confirmPassword'])->setValue($value);
    }
}
