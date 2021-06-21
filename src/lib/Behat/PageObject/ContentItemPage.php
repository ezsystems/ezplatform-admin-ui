<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Core\Environment\EnvironmentConstants;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ContentField;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ContentTypePicker;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
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

    public function __construct(BrowserContext $context, ?string $contentName)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/view/content';
        $this->pageTitle = $contentName;
        $this->pageTitleLocator = '.ez-page-title h1';
        $this->contentTypeLocator = '.ez-page-title h4';
        $this->rightMenu = ElementFactory::createElement($context, RightMenu::ELEMENT_NAME);
        $this->subItemList = ElementFactory::createElement($context, SubItemsList::ELEMENT_NAME, $this->hasGridViewEnabledByDefault());
        $this->contentField = ElementFactory::createElement($context, ContentField::ELEMENT_NAME);
    }

    /**
     * Clicks "Create" and selects Content Type in displayed search.
     *
     * @param string $contentType
     */
    public function startCreatingContent(string $contentTypeName): ContentUpdateItemPage
    {
        $this->rightMenu->clickButton('Create');

        $contentTypePicker = ElementFactory::createElement($this->context, ContentTypePicker::ELEMENT_NAME);
        $contentTypePicker->verifyVisibility();

        if (!$contentTypePicker->isContentTypeVisible($contentTypeName)) {
            $this->handleMissingContentType($contentTypeName);
        }

        ElementFactory::createElement($this->context, ContentTypePicker::ELEMENT_NAME)->select($contentTypeName);

        $contentUpdatePage = PageObjectFactory::createPage($this->context, ContentUpdateItemPage::PAGE_NAME, '');
        $contentUpdatePage->verifyIsLoaded();

        return $contentUpdatePage;
    }

    public function startCreatingUser(): void
    {
        $this->rightMenu->clickButton('Create');
        $contentTypePicker = ElementFactory::createElement($this->context, ContentTypePicker::ELEMENT_NAME);
        $contentTypePicker->verifyVisibility();
        $contentTypePicker->select('User');
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
        $parentContentPage = PageObjectFactory::createPage($this->context, self::PAGE_NAME, null);

        if ($parentContentPage->subItemList->canBeSorted()) {
            $parentContentPage->subItemList->sortBy('Modified', false);
        }
        $parentContentPage->subItemList->table->clickListElement($contentName, $contentType);

        $contentPage = PageObjectFactory::createPage($this->context, self::PAGE_NAME, $contentName);
        $contentPage->verifyIsLoaded();
        $contentPage->verifyContentType($contentType);
    }

    public function navigateToPath(string $path): void
    {
        $pathArray = explode('/', $path);
        $menuTab = $pathArray[0] === EnvironmentConstants::get('ROOT_CONTENT_NAME') ? 'Content structure' : $pathArray[0];

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

    /**
     * @param string $tabName
     */
    public function switchToTab(string $tabName): void
    {
        $this->context->getElementByText($tabName, '#ez-tab-list-location-view .ez-tabs__tab')->click();
    }

    public function addLocation(): void
    {
        $this->context->findElement('#ez-tab-location-view-locations .ez-table-header__tools .btn--udw-add')->click();
    }

    private function hasGridViewEnabledByDefault(): bool
    {
        $pageTitle = $this->pageTitle ?? $this->getPageTitle();

        return $pageTitle === 'Media';
    }

    protected function handleMissingContentType(string $contentTypeName): void
    {
        $contentTypePicker = ElementFactory::createElement($this->context, ContentTypePicker::ELEMENT_NAME);
        $displayedContentTypesBeforeReload = $contentTypePicker->getDisplayedContentTypes();

        $this->context->getSession()->reload();

        $rightMenu = ElementFactory::createElement($this->context, RightMenu::ELEMENT_NAME);
        $rightMenu->clickButton('Create');

        $contentTypePicker = ElementFactory::createElement($this->context, ContentTypePicker::ELEMENT_NAME);
        $displayedContentTypesAfterReload = $contentTypePicker->getDisplayedContentTypes();

        $this->printContentTypes('Content Types before reload:', $displayedContentTypesBeforeReload);
        $this->printContentTypes('Content Types after reload:', $displayedContentTypesAfterReload);

        Assert::fail(sprintf('Content Type: %s was not detected the first time', $contentTypeName));
    }

    protected function printContentTypes(string $initiaMessage, array $contentTypes): void
    {
        foreach ($contentTypes as $contentType) {
            $initiaMessage .= PHP_EOL . $contentType;
        }

        echo $initiaMessage;
    }
}
