<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use Ibexa\AdminUi\Behat\Component\Breadcrumb;
use Ibexa\AdminUi\Behat\Component\ContentItemAdminPreview;
use Ibexa\AdminUi\Behat\Component\ContentTypePicker;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\LanguagePicker;
use Ibexa\AdminUi\Behat\Component\RightMenu;
use Ibexa\AdminUi\Behat\Component\SubItemsList;
use Ibexa\AdminUi\Behat\Component\TranslationDialog;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\AdminUi\Behat\Component\UpperMenu;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class ContentViewPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\RightMenu Element representing the right menu */
    private $rightMenu;

    /** @var \Ibexa\AdminUi\Behat\Component\SubItemsList */
    private $subItemList;

    /** @var string */
    private $locationPath;

    /** @var \Ibexa\AdminUi\Behat\Component\ContentTypePicker */
    private $contentTypePicker;

    /** @var ContentUpdateItemPage */
    private $contentUpdatePage;

    /** @var string */
    private $expectedContentType;

    /** @var \Ibexa\AdminUi\Behat\Component\LanguagePicker */
    private $languagePicker;

    /** @var string */
    private $expectedContentName;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    /** @var \Ibexa\AdminUi\Behat\Component\TranslationDialog */
    private $translationDialog;

    private $route;

    /** @var \Ibexa\AdminUi\Behat\Component\Breadcrumb */
    private $breadcrumb;

    /** @var \Ibexa\AdminUi\Behat\Component\ContentItemAdminPreview */
    private $contentItemAdminPreview;

    /** @var \Ibexa\AdminUi\Behat\Page\UserUpdatePage */
    private $userUpdatePage;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var bool */
    private $expectedIsContainer;

    /** @var \Ibexa\AdminUi\Behat\Component\UpperMenu */
    private $upperMenu;

    /** @var \EzSystems\Behat\Core\Behat\ArgumentParser; */
    private $argumentParser;

    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    public function __construct(
        Session $session, Router $router,
        RightMenu $rightMenu,
        SubItemsList $subItemList,
        ContentTypePicker $contentTypePicker,
        ContentUpdateItemPage $contentUpdatePage,
        LanguagePicker $languagePicker,
        Dialog $dialog,
        TranslationDialog $translationDialog,
        Repository $repository,
        Breadcrumb $breadcrumb,
        ContentItemAdminPreview $contentItemAdminPreview,
        UserUpdatePage $userUpdatePage,
        UpperMenu $upperMenu,
        ArgumentParser $argumentParser,
        UniversalDiscoveryWidget $universalDiscoveryWidget
    ) {
        parent::__construct($session, $router);

        $this->rightMenu = $rightMenu;
        $this->subItemList = $subItemList;
        $this->contentTypePicker = $contentTypePicker;
        $this->contentUpdatePage = $contentUpdatePage;
        $this->languagePicker = $languagePicker;
        $this->dialog = $dialog;
        $this->translationDialog = $translationDialog;
        $this->breadcrumb = $breadcrumb;
        $this->contentItemAdminPreview = $contentItemAdminPreview;
        $this->userUpdatePage = $userUpdatePage;
        $this->repository = $repository;
        $this->upperMenu = $upperMenu;
        $this->argumentParser = $argumentParser;
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
    }

    public function startCreatingContent(string $contentTypeName, string $language = null)
    {
        $this->rightMenu->clickButton('Create');
        $this->contentTypePicker->verifyIsLoaded();
        if ($language !== null) {
            $this->contentTypePicker->selectLanguage($language);
        }
        $this->contentTypePicker->select($contentTypeName);
    }

    public function startCreatingUser()
    {
        $this->rightMenu->clickButton('Create');
        $this->contentTypePicker->verifyIsLoaded();
        $this->contentTypePicker->select('User');
    }

    public function switchToTab(string $tabName): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('tab'))
            ->getByCriterion(new ElementTextCriterion($tabName))
            ->click();
    }

    public function addLocation(string $newLocationPath): void
    {
        $this->getHTMLPage()->find($this->getLocator('addLocationButton'))->click();
        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($newLocationPath);
        $this->universalDiscoveryWidget->confirm();
    }

    public function addTranslation(string $language, string $base): void
    {
        $this->getHTMLPage()->find($this->getLocator('addTranslationButton'))->click();
        $this->translationDialog->verifyIsLoaded();
        $this->translationDialog->selectNewTranslation($language);
        if ($base != 'none') {
            $this->translationDialog->selectBaseTranslation($base);
        }
        $this->translationDialog->confirm();
    }

    public function choosePreview(string $language): void
    {
        $this->getHTMLPage()->find($this->getLocator('previewDropdown'))->click();
        $this->getHTMLPage()
            ->findAll($this->getLocator('previewLanguage'))
            ->getByCriterion(new ElementTextCriterion($language))
            ->click();
        $this->verifyIsLoaded();
    }

    public function goToSubItem(string $contentItemName): void
    {
        $this->subItemList->sortBy('Modified', false);

        $this->subItemList->goTo($contentItemName);
        $this->setExpectedLocationPath(sprintf('%s/%s', $this->locationPath, $contentItemName));
        $this->verifyIsLoaded();
    }

    public function navigateToPath(string $path): void
    {
        $this->verifyIsLoaded();

        $pathParts = explode('/', $path);
        $pathSize = count($pathParts);

        for ($i = 1; $i < $pathSize; ++$i) {
            $this->goToSubItem($pathParts[$i]);
        }
    }

    private function hasGridViewEnabledByDefault(): bool
    {
        return $this->expectedContentName === 'Media';
    }

    public function setExpectedLocationPath(string $locationPath)
    {
        [$this->expectedContentType, $this->expectedContentName, $contentId, $contentMainLocationId, $isContainer] = $this->getContentData($this->argumentParser->parseUrl($locationPath));
        $this->route = sprintf('/view/content/%s/full/1/%s', $contentId, $contentMainLocationId);
        $this->expectedIsContainer = $isContainer;
        $this->locationPath = $locationPath;
        $this->subItemList->shouldHaveGridViewEnabled($this->hasGridViewEnabledByDefault());
    }

    private function getContentData(string $locationPath): array
    {
        return $this->repository->sudo(function (Repository $repository) use ($locationPath) {
            $content = $this->loadContent($repository, $locationPath);

            return [
                $content->getContentType()->getName(),
                $content->getName(),
                $content->id,
                $content->contentInfo->getMainLocation()->id,
                $content->getContentType()->isContainer,
            ];
        });
    }

    private function loadContent(Repository $repository, string $locationPath): Content
    {
        $this->getHTMLPage()->setTimeout(3)->waitUntil(static function () use ($repository, $locationPath) {
            $urlAlias = $repository->getURLAliasService()->lookup($locationPath);

            return $urlAlias->type === URLALias::LOCATION;
        }, sprintf('URLAlias: %s not found in 3 seconds', $locationPath));

        $urlAlias = $repository->getURLAliasService()->lookup($locationPath);
        Assert::assertEquals(URLAlias::LOCATION, $urlAlias->type);

        return $repository->getLocationService()
            ->loadLocation($urlAlias->destination)
            ->getContent();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('mainContainer'))->assert()->isVisible();
        $this->rightMenu->verifyIsLoaded();
        Assert::assertStringContainsString(
            $this->expectedContentName,
            $this->breadcrumb->getBreadcrumb(),
            'Breadcrumb shows invalid path'
        );

        if ($this->expectedIsContainer) {
            $this->subItemList->verifyIsLoaded();
        }

        Assert::assertEquals(
            $this->expectedContentName,
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );

        Assert::assertEquals(
            $this->expectedContentType,
            $this->getHTMLPage()->find($this->getLocator('contentType'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Content view';
    }

    public function editContent(?string $language)
    {
        $this->rightMenu->clickButton('Edit');

        if ($this->languagePicker->isVisible()) {
            $availableLanguages = $this->languagePicker->getLanguages();
            Assert::assertGreaterThan(1, count($availableLanguages));
            Assert::assertContains($language, $availableLanguages);
            $this->languagePicker->chooseLanguage($language);
        }
    }

    public function isChildElementPresent(array $parameters): bool
    {
        return $this->subItemList->isElementInTable($parameters);
    }

    public function sendToTrash()
    {
        $this->rightMenu->clickButton('Send to Trash');
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function verifyFieldHasValues(string $fieldLabel, array $expectedFieldValues, ?string $fieldTypeIdentifier)
    {
        $this->contentItemAdminPreview->verifyFieldHasValues($fieldLabel, $expectedFieldValues, $fieldTypeIdentifier);
    }

    public function bookmarkContentItem(): void
    {
        $this->getHTMLPage()->find($this->getLocator('bookmarkButton'))->click();
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('isBookmarked')));
    }

    public function isBookmarked(): bool
    {
        return $this->getHTMLPage()->find($this->getLocator('isBookmarked'))->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('contentType', '.ez-page-title h4'),
            new VisibleCSSLocator('mainContainer', '#ez-tab-list-content-location-view'),
            new VisibleCSSLocator('tab', '#ez-tab-list-location-view .ez-tabs__tab'),
            new VisibleCSSLocator('addLocationButton', '#ez-tab-location-view-locations .ez-table-header__tools .btn--udw-add'),
            new VisibleCSSLocator('bookmarkButton', '.ez-add-to-bookmarks'),
            new VisibleCSSLocator('isBookmarked', '.ez-add-to-bookmarks--checked'),
            new VisibleCSSLocator('addTranslationButton', '#ez-tab-location-view-translations .ez-table-header__tools .ez-btn--add-translation'),
            new VisibleCSSLocator('previewDropdown', '.ez-location-language-change'),
            new VisibleCSSLocator('previewLanguage', '.ez-location-language-change option'),
        ];
    }

    protected function getRoute(): string
    {
        return $this->route;
    }
}
