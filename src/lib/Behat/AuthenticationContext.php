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
    private $userPasswords = ['admin' => 'publish', 'jessica' => 'publish', 'yura' => 'publish', 'anil' => 'publish'];

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

    /**
     * @Given I am logged as :username
     *
     * @param string $username
     */
    public function iAmLoggedAs(string $username)
    {
        $loginPage = PageObjectFactory::createPage($this->utilityContext, LoginPage::PAGE_NAME);
        $loginPage->open();

        if (!\in_array($username, $this->userPasswords, true))
        {
            // throw;
        }

        $password = $this->userPasswords[$username];
        $loginPage->login($username, $password);
    }
}
