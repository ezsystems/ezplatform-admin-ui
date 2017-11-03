<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Behat;

use EzSystems\EzPlatformAdminUi\Tests\Behat\PageObject\LoginPage;
use EzSystems\EzPlatformAdminUi\Tests\Behat\PageObject\PageObjectFactory;

class AuthenticationContext extends BusinessContext
{
    /**
     * @When I login as :username with password :password
     *
     * @param string $username
     * @param string $password
     */
    public function iLoginAs(string $username, string $password)
    {
        $loginPage = PageObjectFactory::createPage($this->utilityContext, LoginPage::PAGE_NAME);
        $loginPage->login($username, $password);
    }
}
