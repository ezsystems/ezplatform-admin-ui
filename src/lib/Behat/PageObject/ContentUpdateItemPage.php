<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ContentUpdateForm;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use PHPUnit\Framework\Assert;

class ContentUpdateItemPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Content Update';

    /**
     * @var ContentUpdateForm
     */
    public $contentUpdateForm;

    /**
     * @var RightMenu
     */
    public $rightMenu;

    public function __construct(UtilityContext $context, string $contentItemName)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/content';
        $this->contentUpdateForm = ElementFactory::createElement($this->context, ContentUpdateForm::ELEMENT_NAME);
        $this->rightMenu = ElementFactory::createElement($this->context, RightMenu::ELEMENT_NAME);
        $this->pageTitleLocator = '.ez-content-edit-container h1';
        $this->pageTitle = $contentItemName;
    }

    public function verifyElements(): void
    {
        $this->rightMenu->verifyVisibility();
        $this->contentUpdateForm->verifyVisibility();
    }

    public function verifyTitle(): void
    {
        Assert::assertStringEndsWith(
            $this->pageTitle,
            $this->getPageTitle(),
            'Wrong page title.'
        );
    }
}
