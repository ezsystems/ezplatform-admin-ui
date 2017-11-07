<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat;

use EzSystems\EzPlatformAdminUi\Behat\PageObject\LoginPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;

class AuthenticationContext extends BusinessContext
{
    /**
     * @When I login as :username with password :password
     *
     * @param string $username
     * @param string $password
     */
    public function iLoginAs(string $username, string $password): void
    {
        $loginPage = PageObjectFactory::createPage($this->utilityContext, LoginPage::PAGE_NAME);
        $loginPage->login($username, $password);
    }
}
