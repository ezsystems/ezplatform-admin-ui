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
            $this->addItemToMultiSelection($itemName, count($pathParts));
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
        $this->getHTMLPage()->find($this->getLocator('previewButton'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('udw'))->assert()->isVisible();
    }

    protected function isMultiSelect(): bool
    {
        return $this->getHTMLPage()->setTimeout(0)->findAll($this->getLocator('multiselect'))->any();
    }

    protected function addItemToMultiSelection(string $itemName, int $level): void
    {
        $treeElementsLocator = new CSSLocator('', sprintf($this->getLocator('treeLevelElementsFormat')->getSelector(), $level));
        $this->getHTMLPage()->findAll($treeElementsLocator)->getByCriterion(new ElementTextCriterion($itemName))->find($this->getLocator('input'))->click();
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

        $this->getHTMLPage()->findAll($treeElementsLocator)->getByCriterion(new ElementTextCriterion($itemName))->find($this->getLocator('elementName'))->click();
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

    protected function specifyLocators(): array
    {
        return [
            // general selectors
            new VisibleCSSLocator('udw', '.m-ud'),
            new CSSLocator('confirmButton', '.c-actions-menu__confirm-btn'),
            new CSSLocator('cancelButton', '.c-top-menu__cancel-btn'),
            new CSSLocator('mainWindow', '.m-ud'),
            new CSSLocator('selectedLocationsTab', '.c-selected-locations'),
            new VisibleCSSLocator('multiselect', '.m-ud .c-finder-leaf .ibexa-input--checkbox'),
            // selectors for path traversal
            new CSSLocator('treeLevelFormat', '.c-finder-branch:nth-child(%d)'),
            new CSSLocator('treeLevelElementsFormat', '.c-finder-branch:nth-of-type(%d) .c-finder-leaf'),
            new CSSLocator('elementName', '.c-finder-leaf__name'),
            new CSSLocator('input', '.c-udw-toggle-selection'),
            new CSSLocator('treeLevelSelectedFormat', '.c-finder-branch:nth-of-type(%d) .c-finder-leaf--marked'),
            // itemActions
            new CSSLocator('previewButton', '.c-content-meta-preview__preview-button'),
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
