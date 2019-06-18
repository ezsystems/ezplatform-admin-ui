<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminUpdateForm;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use PHPUnit\Framework\Assert;

class AdminUpdateItemPage extends Page
{
    /** @var string Route under which the Page is available */
    protected $route = '/admin/contenttypegroup';

    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Admin Item Update';

    /**
     * @var AdminUpdateForm
     */
    public $adminUpdateForm;

    /**
     * @var RightMenu
     */
    public $rightMenu;

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->adminUpdateForm = ElementFactory::createElement($this->context, AdminUpdateForm::ELEMENT_NAME);
        $this->rightMenu = ElementFactory::createElement($this->context, RightMenu::ELEMENT_NAME);
        $this->pageTitle = 'Editing';
    }

    public function verifyElements(): void
    {
        $this->rightMenu->verifyVisibility();
        $this->adminUpdateForm->verifyVisibility();
    }

    public function verifyTitle(): void
    {
        Assert::assertContains(
            $this->pageTitle,
            $this->getPageTitle(),
            'Wrong page title.'
        );
    }
}
