<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Ibexa\AdminUi\Behat\Page\ChangePasswordPage;

class UserPreferencesContext implements Context
{
    /**
     * @var \Ibexa\AdminUi\Behat\Page\ChangePasswordPage
     */
    private $changePasswordPage;
    /**
     * @var UserSettingsPage
     */
    private $userSettingsPage;

    public function __construct(ChangePasswordPage $changePasswordPage, UserSettingsPage $userSettingsPage)
    {
        $this->changePasswordPage = $changePasswordPage;
        $this->userSettingsPage = $userSettingsPage;
    }

    /**
     * @When I change password from :oldPassword to :newPassword
     */
    public function iChangePassword($oldPassword, $newPassword): void
    {
        $this->changePasswordPage->verifyIsLoaded();
        $this->changePasswordPage->setOldPassword($oldPassword);
        $this->changePasswordPage->setNewPassword($newPassword);
        $this->changePasswordPage->setConfirmPassword($newPassword);
    }

    /**
     * @When I disable autosave
     */
    public function iSetAutosaveDraftValue(): void
    {
        $this->userSettingsPage->verifyIsLoaded();
        $this->userSettingsPage->openAutosaveDraftEditionPage();
        $this->userSettingsPage->disableAutosave();
    }
}
