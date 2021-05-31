<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageObject\ChangePasswordPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;

class UserPreferencesContext extends BusinessContext
{
    /**
     * @When I change password from :oldPassword to :newPassword
     */
    public function iChangePassword($oldPassword, $newPassword): void
    {
        $passwordPage = PageObjectFactory::createPage($this->browserContext, ChangePasswordPage::PAGE_NAME);
        $passwordPage->verifyIsLoaded();
        $passwordPage->setOldPassword($oldPassword);
        $passwordPage->setNewPassword($newPassword);
        $passwordPage->setConfirmPassword($newPassword);
    }
}
