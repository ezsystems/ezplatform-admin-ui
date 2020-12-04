<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ContentUpdateForm;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use PHPUnit\Framework\Assert;

class UserCreationPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Creating - User';

    /**
     * @var ContentUpdateForm
     */
    public $contentUpdateForm;

    /**
     * @var RightMenu
     */
    public $rightMenu;

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->contentUpdateForm = ElementFactory::createElement($this->context, ContentUpdateForm::ELEMENT_NAME);
        $this->rightMenu = ElementFactory::createElement($this->context, RightMenu::ELEMENT_NAME);
        $this->pageTitleLocator = '.ez-content-item-status';
    }

    public function verifyElements(): void
    {
        $this->rightMenu->verifyVisibility();
        $this->contentUpdateForm->verifyVisibility();
    }

    public function verifyTitle(): void
    {
        $expectedPageTitles = ['Creating a(n) User', 'Editing a(n) User'];
        Assert::assertContains($this->getPageTitle(), $expectedPageTitles);
    }

    public function verifyRoute(): void
    {
        $expectedPageRoutes = ['/user/create/user', '/user/update'];
        $actualRoute = $this->getCurrentRoute();
        foreach ($expectedPageRoutes as $expectedPageRoute) {
            if (strpos($actualRoute, $expectedPageRoute) !== false) {
                return;
            }
        }
        Assert::fail(sprintf('Expected one of: %s. Actual: %s', implode(',', $expectedPageRoutes), $actualRoute));
    }

    public function verifyIsLoaded(): void
    {
        $this->verifyElements();
        $this->verifyRoute();
        $this->verifyTitle();
    }
}
