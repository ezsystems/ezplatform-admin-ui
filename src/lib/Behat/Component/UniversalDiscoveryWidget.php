<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class UniversalDiscoveryWidget extends Component
{
    private const LONG_TIMEOUT = 20;
    private const SHORT_TIMEOUT = 2;

    public function selectContent(string $itemPath): void
    {
        $pathParts = explode('/', $itemPath);
        $level = 1;

        foreach ($pathParts as $itemName) {
            $this->selectTreeBranch($itemName, $level);
            ++$level;
        }

        $itemName = $pathParts[count($pathParts) - 1];

        if ($this->isMultiSelect()) {
            $this->addItemToMultiselection($itemName, count($pathParts));
        }
    }

    public function confirm(): void
    {
        $this->getHTMLPage()->find($this->getLocator('confirmButton'))->click();
    }

    public function cancel(): void
    {
        $this->getHTMLPage()->find($this->getLocator('cancelButton'))->click();
    }

    public function openPreview(): void
    {
        $this->getHTMLPage()->setTimeout(self::SHORT_TIMEOUT)->find($this->getLocator('previewButton'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $expectedTabTitles = ['Browse', 'Bookmarks', 'Search'];

        $tabs = $this->getHTMLPage()->findAll($this->getLocator('categoryTabSelector'));
        $foundExpectedTitles = [];
        foreach ($tabs as $tab) {
            $tabText = $tab->getText();
            if (in_array($tabText, $expectedTabTitles)) {
                $foundExpectedTitles[] = $tabText;
            }
        }

        Assert::assertEquals($expectedTabTitles, $foundExpectedTitles);
    }

    protected function isMultiSelect(): bool
    {
        return $this->getHTMLPage()
            ->setTimeout(self::SHORT_TIMEOUT)
            ->findAll($this->getLocator('multiSelectAddButton'))
            ->any()
        ;
    }

    protected function addItemToMultiSelection(string $itemName, int $level): void
    {
        $currentSelectedItemLocator = new CSSLocator('currentSelectedItem', sprintf($this->getLocator('treeLevelSelectedFormat')->getSelector(), $level));
        $this->getHTMLPage()->findAll($currentSelectedItemLocator)->getByCriterion(new ElementTextCriterion($itemName))->mouseOver();

        $addItemLocator = new CSSLocator('addItemLocator', sprintf($this->getLocator('currentlySelectedAddItemButtonFormat')->getSelector(), $level));
        $this->getHTMLPage()->find($addItemLocator)->click();

        $addedItemLocator = new CSSLocator('addedItemLocator', sprintf($this->getLocator('currentlySelectedItemAddedFormat')->getSelector(), $level));
        Assert::assertTrue($this->getHTMLPage()->find($addedItemLocator)->isVisible());
    }

    protected function selectTreeBranch(string $itemName, int $level): void
    {
        $treeLevelLocator = new VisibleCSSLocator('treeLevelLocator', sprintf($this->getLocator('treeLevelElementsFormat')->getSelector(), $level));
        $this->getHTMLPage()->setTimeout(self::LONG_TIMEOUT)->find($treeLevelLocator)->assert()->isVisible();

        $alreadySelectedItemName = $this->getCurrentlySelectedItemName($level);

        if ($itemName === $alreadySelectedItemName) {
            // don't do anything, this level is already selected

            return;
        }

        // when the tree is loaded further for the already selected item we need to make sure it's reloaded properly
        $willNextLevelBeReloaded = null !== $alreadySelectedItemName && $this->isNextLevelDisplayed($level);

        if ($willNextLevelBeReloaded) {
            $currentItems = $this->getItemsFromLevel($level + 1);
        }

        $treeElementsLocator = new CSSLocator('', sprintf($this->getLocator('treeLevelElementsFormat')->getSelector(), $level));
        $selectedTreeElementLocator = new CSSLocator('', sprintf($this->getLocator('treeLevelSelectedFormat')->getSelector(), $level));

        $this->getHTMLPage()->findAll($treeElementsLocator)->getByCriterion(new ElementTextCriterion($itemName))->click();
        $this->getHTMLPage()->findAll($selectedTreeElementLocator)->getByCriterion(new ElementTextCriterion($itemName))->assert()->isVisible();

        if ($willNextLevelBeReloaded) {
            // Wait until the items displayed previously disappear or change
            $this->getHTMLPage()->setTimeout(self::LONG_TIMEOUT)->waitUntil(function () use ($currentItems, $level) {
                return !$this->isNextLevelDisplayed($level) || $this->getItemsFromLevel($level + 1) !== $currentItems;
            }, 'Items in UDW did not refresh correctly');
        }
    }

    protected function getItemsFromLevel(int $level): array
    {
        $levelItemsSelector = new CSSLocator('css', sprintf($this->getLocator('treeLevelElementsFormat')->getSelector(), $level));

        return $this->getHTMLPage()->setTimeout(self::LONG_TIMEOUT)->findAll($levelItemsSelector)->map(
            static function (ElementInterface $element) {
                return $element->getText();
            }
        );
    }

    public function bookmarkContentItem(): void
    {
        $this->getHTMLPage()->find($this->getLocator('bookmarkButton'))->click();
        $this->getHTMLPage()->setTimeout(3)->waitUntil(function () {
            return $this->isBookmarked();
        }, 'The icon did not change to bookmarked one');
    }

    public function isBookmarked(): bool
    {
        $htmlFragment = $this->getHTMLPage()
            ->find($this->getLocator('bookmarkButton'))
            ->getOuterHtml();

        return strpos($htmlFragment, 'bookmark-active') !== false;
    }

    public function changeTab(string $tabName): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('categoryTabSelector'))
             ->getByCriterion(new ElementTextCriterion($tabName))
             ->click();

        $this->getHTMLPage()->find($this->getLocator('selectedTab'))->assert()->textEquals($tabName);
    }

    public function selectBookmark(string $bookmarkName): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('bookmarkedItem'))
             ->getByCriterion(new ElementTextCriterion($bookmarkName))
             ->click();

        $this->getHTMLPage()->find($this->getLocator('markedBookmarkedItem'))->assert()->textEquals($bookmarkName);
    }

    protected function specifyLocators(): array
    {
        return [
            // general selectors
            new CSSLocator('confirmButton', '.c-selected-locations__confirm-button'),
            new CSSLocator('categoryTabSelector', '.c-tab-selector__item'),
            new CSSLocator('cancelButton', '.c-top-menu__cancel-btn'),
            new CSSLocator('mainWindow', '.m-ud'),
            new CSSLocator('selectedLocationsTab', '.c-selected-locations'),
            new CSSLocator('selectedTab', '.c-tab-selector__item--selected'),
            // selectors for path traversal
            new CSSLocator('treeLevelFormat', '.c-finder-branch:nth-child(%d)'),
            new CSSLocator('treeLevelElementsFormat', '.c-finder-branch:nth-of-type(%d) .c-finder-leaf'),
            new CSSLocator('treeLevelSelectedFormat', '.c-finder-branch:nth-of-type(%d) .c-finder-leaf--marked'),
            // selectors for multiitem selection
            new CSSLocator('multiSelectAddButton', '.c-toggle-selection-button'),
            // itemActions
            new CSSLocator('previewButton', '.c-content-meta-preview__preview-button'),
            new CSSLocator('currentlySelectedItemAddedFormat', '.c-finder-branch:nth-of-type(%d) .c-finder-leaf--marked .c-toggle-selection-button.c-toggle-selection-button--selected'),
            new CSSLocator('currentlySelectedAddItemButtonFormat', '.c-finder-branch:nth-of-type(%d) .c-finder-leaf--marked .c-toggle-selection-button'),
            // bookmarks
            new VisibleCSSLocator('bookmarkButton', '.c-content-meta-preview__toggle-bookmark-button'),
            new VisibleCSSLocator('bookmarkedItem', '.c-bookmarks-list__item-name'),
            new VisibleCSSLocator('markedBookmarkedItem', '.c-bookmarks-list__item--marked'),
        ];
    }

    private function getCurrentlySelectedItemName(int $level): ?string
    {
        $selectedElementSelector = new CSSLocator(
            'selectedElement',
            sprintf($this->getLocator('treeLevelSelectedFormat')->getSelector(), $level)
        );

        $elements = $this->getHTMLPage()->setTimeout(self::SHORT_TIMEOUT)->findAll($selectedElementSelector);

        return $elements->any() ? $elements->first()->getText() : null;
    }

    private function isNextLevelDisplayed(int $currentLevel): bool
    {
        return $this->getHTMLPage()->
            setTimeout(self::SHORT_TIMEOUT)->
            findAll(
                new CSSLocator(
                    'css',
                    sprintf($this->getLocator('treeLevelElementsFormat')->getSelector(), $currentLevel + 1)
                )
            )->any();
    }
}
