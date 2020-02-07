<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\Core\Environment\EnvironmentConstants;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Breadcrumb;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\DraftConflictDialog;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LanguagePicker;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentItemPage;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use PHPUnit\Framework\Assert;

class ContentViewContext extends BusinessContext
{
    private $argumentParser;

    /**
     * @injectService $argumentParser @EzSystems\Behat\Core\Behat\ArgumentParser
     */
    public function __construct(ArgumentParser $argumentParser)
    {
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Given I start creating a new content :contentType
     */
    public function startCreatingContent(string $contentType): void
    {
        PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, 'Home')->startCreatingContent($contentType);
    }

    /**
     * @Given I start editing the current content
     * @Given I start editing the current content in :language language
     */
    public function startEditingContent(string $language = null): void
    {
        $rightMenu = new RightMenu($this->browserContext);
        $rightMenu->clickButton('Edit');

        $languagePicker = ElementFactory::createElement($this->browserContext, LanguagePicker::ELEMENT_NAME);

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
        $leftMenu = new LeftMenu($this->browserContext);
        $leftMenu->verifyVisibility();
        $leftMenu->clickButton('Browse');

        $udw = ElementFactory::createElement($this->browserContext, UniversalDiscoveryWidget::ELEMENT_NAME);
        $udw->verifyVisibility();
        $udw->selectContent($this->argumentParser->replaceRootKeyword($itemPath));
        $udw->confirm();
    }

    /**
     * @Then I (should) see :title title/topic
     */
    public function iSeeTitle(string $title): void
    {
        $contentItemPage = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, 'Home');
        Assert::assertEquals($title, $contentItemPage->getPageTitle());
    }

    /**
     * Check if item has or not sub-item, according to $itemShouldExist param.
     *
     * @param string $itemName
     * @param string $itemType
     * @param string $containerName
     * @param bool $itemShouldExist
     */
    private function verifyItemExistenceInSubItemList(string $itemName, string $itemType, string $containerName, bool $itemShouldExist): void
    {
        $isItemInTable = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, $containerName)
            ->subItemList->table->isElementInTable($itemName);

        $itemShouldExistString = $itemShouldExist ? '' : 'n\'t';

        if ($isItemInTable !== $itemShouldExist) {
            Assert::fail(sprintf('%s "%s" should%s be on %s Sub-items list', $itemType, $itemName, $itemShouldExistString, $containerName));
        }
    }

    /**
     * @Then there's :itemName :itemType on :containerName Sub-items list
     */
    public function verifyThereIsItemInSubItemList(string $itemName, string $itemType, string $containerName): void
    {
        $this->verifyItemExistenceInSubitemList($itemName, $itemType, $containerName, true);
    }

    /**
     * @Then there's no :itemName :itemType on :containerName Sub-items list
     */
    public function verifyThereIsNoItemInSubItemList(string $itemName, string $itemType, string $containerName): void
    {
        $this->verifyItemExistenceInSubitemList($itemName, $itemType, $containerName, false);
    }

    /**
     * @Then there's no :itemName :itemType on Sub-items list of root
     */
    public function verifyThereIsNoItemInSubItemListInRoot(string $itemName, string $itemType): void
    {
        $this->verifyThereIsNoItemInSubItemList($itemName, $itemType, EnvironmentConstants::get('ROOT_CONTENT_NAME'));
    }

    /**
     * @Given I should be on content item page :contentName of type :contentType
     * @Given I should be on content item page :contentName of type :contentType in :path
     */
    public function verifyImOnContentItemPage(string $contentName, string $contentType, ?string $path = null)
    {
        $path = !$path ? $contentName : $path . '/' . $contentName;
        $path = $this->argumentParser->replaceRootKeyword($path);
        $spacedPath = str_replace('/', ' ', $path);

        $contentPage = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, $contentName);
        $contentPage->verifyIsLoaded();
        $contentPage->verifyContentType($contentType);
        $breadcrumb = ElementFactory::createElement($this->browserContext, Breadcrumb::ELEMENT_NAME);
        Assert::assertEquals($spacedPath, $breadcrumb->getBreadcrumb(), 'Wrong content location');
    }

    /**
     * @Given I should be on content item page :contentName of type :contentType in root path
     */
    public function verifyImOnContentItemPageInRoot(string $contentName, string $contentType)
    {
        $this->verifyImOnContentItemPage($contentName, $contentType, EnvironmentConstants::get('ROOT_CONTENT_NAME'));
    }

    /**
     * @Given I should be on content container page :contentName of type :contentType
     * @Given I should be on content container page :contentName of type :contentType in :path
     */
    public function verifyImOnContentContainerPage(string $contentName, string $contentType, ?string $path = null)
    {
        $this->verifyImOnContentItemPage($contentName, $contentType, $path);

        PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, $contentName)->verifySubItemListVisibility();
    }

    /**
     * @Given I should be on content container page :contentName of type :contentType in root path
     */
    public function verifyImOnContentContainerPageInRoot(string $contentName, string $contentType)
    {
        $this->verifyImOnContentContainerPage($contentName, $contentType, EnvironmentConstants::get('ROOT_CONTENT_NAME'));
    }

    /**
     * @Given I should be on root container page in Content View
     */
    public function verifyImOnRootPage()
    {
        $contentName = EnvironmentConstants::get('ROOT_CONTENT_NAME');
        $contentType = EnvironmentConstants::get('ROOT_CONTENT_TYPE');

        $this->verifyImOnContentContainerPage($contentName, $contentType);
    }

    /**
     * @Then content attributes equal
     */
    public function contentAttributesEqual(TableNode $parameters): void
    {
        $hash = $parameters->getHash();
        $contentItemPage = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, '');
        foreach ($hash as $fieldData) {
            $contentItemPage->contentField->verifyFieldHasValue($fieldData['label'], $fieldData);
        }
    }

    /**
     * @Then article main content field equals :intro
     */
    public function articleMainContentFieldEquals(string $intro): void
    {
        $contentItemPage = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, '');
        $fieldName = EnvironmentConstants::get('ARTICLE_MAIN_FIELD_NAME');
        $contentItemPage->contentField->verifyFieldHasValue($fieldName, ['value' => $intro]);
    }

    /**
     * @When I start creating new draft from draft conflict modal
     */
    public function startCreatingNewDraftFromDraftConflictModal(): void
    {
        $draftConflictModal = ElementFactory::createElement($this->browserContext, DraftConflictDialog::ELEMENT_NAME);
        $draftConflictModal->verifyVisibility();
        $draftConflictModal->createNewDraft();
    }

    /**
     * @When I start editing draft with ID :draftID from draft conflict modal
     */
    public function startEditingDraftFromDraftConflictModal(string $draftID): void
    {
        $draftConflictModal = ElementFactory::createElement($this->browserContext, DraftConflictDialog::ELEMENT_NAME);
        $draftConflictModal->verifyVisibility();
        $draftConflictModal->draftConflictTable->clickEditButton($draftID);
    }

    /**
     * @Then going to :path there is no :contentName :contentType on Sub-items list
     */
    public function goingToPathTheresNoSubItem(string $path, string $contentName, string $contentType): void
    {
        $contentPage = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, $contentName);
        $contentPage->navigateToPath($path);

        $explodedPath = explode('/', $path);

        $this->verifyThereIsNoItemInSubItemList($contentName, $contentType, $explodedPath[count($explodedPath) - 1]);
    }

    /**
     * @Then going to root path there is no :contentName :contentType on Sub-items list
     */
    public function goingToRootTheresNoSubItem(string $contentName, string $contentType): void
    {
        $this->goingToPathTheresNoSubItem(EnvironmentConstants::get('ROOT_CONTENT_NAME'), $contentName, $contentType);
    }

    /**
     * @Then going to :path there is a :contentName :contentType on Sub-items list
     */
    public function goingToPathTheresSubItem(string $path, string $contentName, string $contentType): void
    {
        $contentPage = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, $contentName);
        $contentPage->navigateToPath($path);

        $explodedPath = explode('/', $path);
        $pathSize = count($explodedPath);

        $contentItemPage = PageObjectFactory::createPage($this->browserContext, ContentItemPage::PAGE_NAME, $explodedPath[$pathSize - 1]);

        Assert::assertTrue(
            $contentItemPage->subItemList->table->isElementInTable($contentName),
            sprintf('%s "%s" shouldn\'t be on %s Sub-items list', $contentType, $contentName, $explodedPath[$pathSize - 1])
        );
    }

    /**
     * @Then going to root path there is :contentName :contentType on Sub-items list
     */
    public function goingToRootTheresSubItem(string $contentName, string $contentType): void
    {
        $this->goingToPathTheresSubItem(EnvironmentConstants::get('ROOT_CONTENT_NAME'), $contentName, $contentType);
    }

    /**
     * @When I send content to trash
     */
    public function iSendContentToTrash(): void
    {
        $rightMenu = ElementFactory::createElement($this->browserContext, RightMenu::ELEMENT_NAME);
        $rightMenu->clickButton('Send to Trash');

        $dialog = ElementFactory::createElement($this->browserContext, Dialog::ELEMENT_NAME);
        $dialog->confirm();
    }
}
