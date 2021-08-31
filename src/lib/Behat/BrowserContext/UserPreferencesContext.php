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
    /** @var \Ibexa\AdminUi\Behat\Page\ChangePasswordPage */
    private $changePasswordPage;

    public function __construct(ChangePasswordPage $changePasswordPage)
    {
        $this->changePasswordPage = $changePasswordPage;
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
}
