<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\EzEnvironmentConstants;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ContentField;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ContentTypePicker;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\SubItemsList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu;
use PHPUnit\Framework\Assert;

class ContentItemPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'ContentItemPage';

    /** @var RightMenu Element representing the right menu */
    public $rightMenu;

    /** @var SubItemsList */
    public $subItemList;

    /** @var ContentField */
    public $contentField;

    /** @var string */
    public $contentTypeLocator;

    public function __construct(UtilityContext $context, string $contentName)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/content/location';
        $this->rightMenu = ElementFactory::createElement($context, RightMenu::ELEMENT_NAME);
        $this->subItemList = ElementFactory::createElement($context, SubItemsList::ELEMENT_NAME);
        $this->contentField = ElementFactory::createElement($context, ContentField::ELEMENT_NAME);
        $this->pageTitle = $contentName;
        $this->pageTitleLocator = '.ez-page-title h1';
        $this->contentTypeLocator = '.ez-page-title h4';
    }

    /**
     * Clicks "Create" and selects Content Type in displayed search.
     *
     * @param string $contentType
     */
    public function startCreatingContent(string $contentType): ContentUpdateItemPage
    {
        $this->rightMenu->clickButton('Create');

        $contentTypePicker = ElementFactory::createElement($this->context, ContentTypePicker::ELEMENT_NAME);
        $contentTypePicker->select($contentType);

        $contentUpdatePage = PageObjectFactory::createPage($this->context, ContentUpdateItemPage::PAGE_NAME, $contentType);
        $contentUpdatePage->verifyIsLoaded();

        return $contentUpdatePage;
    }

    public function verifyElements(): void
    {
        $this->rightMenu->verifyVisibility();
    }

    public function verifySubItemListVisibility(): void
    {
        $this->subItemList->verifyVisibility();
    }

    public function verifyContentType(string $contentType): void
    {
        Assert::assertEquals(
            $contentType,
            $this->context->findElement($this->contentTypeLocator)->getText(),
            'Wrong content type'
        );
    }

    public function goToSubItem(string $contentName, string $contentType): void
    {
        $contentPage = PageObjectFactory::createPage($this->context, self::PAGE_NAME, $contentName);
        $contentPage->subItemList->table->clickListElement($contentName, $contentType);
        $contentPage->verifyIsLoaded();
        $contentPage->verifyContentType($contentType);
    }

    public function navigateToPath(string $path): void
    {
        $pathArray = explode('/', $path);
        $menuTab = $pathArray[0] === EzEnvironmentConstants::get('ROOT_CONTENT_NAME') ? 'Content structure' : $pathArray[0];

        $upperMenu = ElementFactory::createElement($this->context, UpperMenu::ELEMENT_NAME);
        $upperMenu->goToTab('Content');
        $upperMenu->goToSubTab($menuTab);

        $pathSize = count($pathArray);
        if ($pathSize > 1) {
            for ($i = 1; $i < $pathSize; ++$i) {
                $contentPage = PageObjectFactory::createPage($this->context, self::PAGE_NAME, $pathArray[$i - 1]);
                $contentPage->verifyIsLoaded();
                $contentPage->subItemList->table->clickListElement($pathArray[$i]);
            }
        }
    }
}
