<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\Helper\EzEnvironmentConstants;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Breadcrumb;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\DraftConflictDialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LanguagePicker;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentItemPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use PHPUnit\Framework\Assert;

class ContentViewContext extends BusinessContext
{
    /**
     * @Given I start creating a new content :contentType
     */
    public function startCreatingContent(string $contentType): void
    {
        PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, 'Home')->startCreatingContent($contentType);
    }

    /**
     * @Given I start editing the current content
     * @Given I start editing the current content in :language language
     */
    public function startEditingContent(string $language = null): void
    {
        $rightMenu = new RightMenu($this->utilityContext);
        $rightMenu->clickButton('Edit');

        $languagePicker = ElementFactory::createElement($this->utilityContext, LanguagePicker::ELEMENT_NAME);

        if ($languagePicker->isVisible()) {
            $availableLanguages = $languagePicker->getLanguages();
            Assert::assertGreaterThan(1, count($availableLanguages));
            Assert::assertContains($language, $availableLanguages);
            $languagePicker->chooseLanguage($language);
        }
    }

    /**
     * @Given I open UDW and go to :itemPath
     */
    public function iOpenUDWAndGoTo(string $itemPath): void
    {
        $leftMenu = new LeftMenu($this->utilityContext);
        $leftMenu->verifyVisibility();
        $leftMenu->clickButton('Browse');

        $udw = ElementFactory::createElement($this->utilityContext, UniversalDiscoveryWidget::ELEMENT_NAME);
        $udw->verifyVisibility();
        $udw->selectContent($itemPath);
        $udw->confirm();
    }

    /**
     * @Then I (should) see :title title/topic
     */
    public function iSeeTitle(string $title): void
    {
        $contentItemPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, 'Home');
        Assert::assertEquals($title, $contentItemPage->getPageTitle());
    }

    /**
     * @Then there's no :itemName :itemType on :folder Sub-items list
     */
    public function verifyThereIsNoItemInSubItemList(string $itemName, string $itemType, string $folder): void
    {
        $contentItemPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, $folder);

        Assert::assertFalse(
            $contentItemPage->subItemList->table->isElementInTable($itemName, $itemType),
            sprintf('%s "%s" shouldn\'t be on %s Sub-items list', $itemType, $itemName, $folder)
        );
    }

    /**
     * @Then there's no :itemName :itemType on Sub-items list of root
     */
    public function verifyThereIsNoItemInSubItemListInRoot(string $itemName, string $itemType): void
    {
        $this->verifyThereIsNoItemInSubItemList($itemName, $itemType, EzEnvironmentConstants::get('ROOT_CONTENT_NAME'));
    }

    /**
     * @Given I should be on content item page :contentName of type :contentType
     * @Given I should be on content item page :contentName of type :contentType in :path
     */
    public function verifyImOnContentItemPage(string $contentName, string $contentType, ?string $path = null)
    {
        $path = !$path ? $contentName : $path . '/' . $contentName;
        $spacedPath = str_replace('/', ' ', $path);

        $contentPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, $contentName);
        $contentPage->verifyIsLoaded();
        $contentPage->verifyContentType($contentType);
        $breadcrumb = ElementFactory::createElement($this->utilityContext, Breadcrumb::ELEMENT_NAME);
        Assert::assertEquals($spacedPath, $breadcrumb->getBreadcrumb(), 'Wrong content location');
    }

    /**
     * @Given I should be on content item page :contentName of type :contentType in root path
     */
    public function verifyImOnContentItemPageInRoot(string $contentName, string $contentType)
    {
        $this->verifyImOnContentItemPage($contentName, $contentType, EzEnvironmentConstants::get('ROOT_CONTENT_NAME'));
    }

    /**
     * @Given I should be on content container page :contentName of type :contentType
     * @Given I should be on content container page :contentName of type :contentType in :path
     */
    public function verifyImOnContentContainerPage(string $contentName, string $contentType, ?string $path = null)
    {
        $this->verifyImOnContentItemPage($contentName, $contentType, $path);

        PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, $contentName)->verifySubItemListVisibility();
    }

    /**
     * @Given I should be on content container page :contentName of type :contentType in root path
     */
    public function verifyImOnContentContainerPageInRoot(string $contentName, string $contentType)
    {
        $this->verifyImOnContentContainerPage($contentName, $contentType, EzEnvironmentConstants::get('ROOT_CONTENT_NAME'));
    }

    /**
     * @Given I should be on root container page in Content View
     */
    public function verifyImOnRootPage()
    {
        $contentName = EzEnvironmentConstants::get('ROOT_CONTENT_NAME');
        $contentType = EzEnvironmentConstants::get('ROOT_CONTENT_TYPE');

        $this->verifyImOnContentContainerPage($contentName, $contentType);
    }

    /**
     * @Then content attributes equal
     */
    public function contentAttributesEqual(TableNode $parameters): void
    {
        $hash = $parameters->getHash();
        $contentItemPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, '');
        foreach ($hash as $field) {
            $contentItemPage->contentField->verifyFieldHasValue($field['label'], $field);
        }
    }

    /**
     * @Then article main content field equals :intro
     */
    public function articleMainContentFieldEquals(string $intro): void
    {
        $contentItemPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, '');
        $fieldName = EzEnvironmentConstants::get('ARTICLE_MAIN_FIELD_NAME');
        $contentItemPage->contentField->verifyFieldHasValue($fieldName, ['value' => $intro]);
    }

    /**
     * @When I start creating new draft from draft conflict modal
     */
    public function startCreatingNewDraftFromDraftConflictModal(): void
    {
        $draftConflictModal = ElementFactory::createElement($this->utilityContext, DraftConflictDialog::ELEMENT_NAME);
        $draftConflictModal->verifyVisibility();
        $draftConflictModal->createNewDraft();
    }

    /**
     * @When I start editing draft with ID :draftID from draft conflict modal
     */
    public function startEditingDraftFromDraftConflictModal(string $draftID): void
    {
        $draftConflictModal = ElementFactory::createElement($this->utilityContext, DraftConflictDialog::ELEMENT_NAME);
        $draftConflictModal->verifyVisibility();
        $draftConflictModal->dashboardTable->clickEditButton($draftID);
    }

    /**
     * @Then going to :path there is no :contentName :contentType on Sub-items list
     */
    public function goingToPathTheresNoSubItem(string $path, string $contentName, string $contentType): void
    {
        $contentPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, $contentName);
        $contentPage->navigateToPath($path);

        $explodedPath = explode('/', $path);

        $this->verifyThereIsNoItemInSubItemList($contentName, $contentType, $explodedPath[count($explodedPath) - 1]);
    }

    /**
     * @Then going to root path there is no :contentName :contentType on Sub-items list
     */
    public function goingToRootTheresNoSubItem(string $contentName, string $contentType): void
    {
        $this->goingToPathTheresNoSubItem(EzEnvironmentConstants::get('ROOT_CONTENT_NAME'), $contentName, $contentType);
    }

    /**
     * @Then going to :path there is a :contentName :contentType on Sub-items list
     */
    public function goingToPathTheresSubItem(string $path, string $contentName, string $contentType): void
    {
        $contentPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, $contentName);
        $contentPage->navigateToPath($path);

        $explodedPath = explode('/', $path);
        $pathSize = count($explodedPath);

        $contentItemPage = PageObjectFactory::createPage($this->utilityContext, ContentItemPage::PAGE_NAME, $explodedPath[$pathSize - 1]);

        Assert::assertTrue(
            $contentItemPage->subItemList->table->isElementInTable($contentName, $contentType),
            sprintf('%s "%s" shouldn\'t be on %s Sub-items list', $contentType, $contentName, $explodedPath[$pathSize - 1])
        );
    }

    /**
     * @When I send content to trash
     */
    public function iSendContentToTrash(): void
    {
        $rightMenu = ElementFactory::createElement($this->utilityContext, RightMenu::ELEMENT_NAME);
        $rightMenu->clickButton('Send to Trash');

        $dialog = ElementFactory::createElement($this->utilityContext, Dialog::ELEMENT_NAME);
        $dialog->confirm();
    }
}
