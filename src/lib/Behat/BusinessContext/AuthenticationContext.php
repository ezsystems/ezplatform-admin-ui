<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageObject\LoginPage;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use OutOfBoundsException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Behat\Symfony2Extension\Context\KernelDictionary;

/** Context for authentication actions */
class AuthenticationContext extends BusinessContext
{
    use KernelDictionary;

    /** @var array Dictionary of known user logins and their passwords */
    private $userCredentials = [
        'admin' => 'publish',
        'jessica' => 'publish',
        'yura' => 'publish',
        'anil' => 'publish',
    ];

    /**
     * @When I login as :username with password :password
     */
    public function iLoginAs(string $username, string $password): void
    {
        $loginPage = PageObjectFactory::createPage($this->browserContext, LoginPage::PAGE_NAME);
        $loginPage->login($username, $password);
    }

    /**
     * @Given I am logged as :username
     */
    public function iAmLoggedAs(string $username): void
    {
        $loginPage = PageObjectFactory::createPage($this->browserContext, LoginPage::PAGE_NAME);
        $loginPage->open();

        if (!\array_key_exists($username, $this->userCredentials)) {
            throw new OutOfBoundsException('Login is not recognised');
        }

        $password = $this->userCredentials[$username];
        $loginPage->login($username, $password);
    }

    /** 
     * @Given I regenerate GraphQL schema
     */
    public function test(): void
    {
        $application = new Application($this->getKernel());
        $application->setAutoExit(false);
        $input = new ArrayInput(['command' => "ezplatform:graphql:generate-schema"]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        var_dump($content);

        $application = new Application($this->getKernel());
        $application->setAutoExit(false);
        $input = new ArrayInput(['command' => "cache:clear"]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        var_dump($content);
    }
}
