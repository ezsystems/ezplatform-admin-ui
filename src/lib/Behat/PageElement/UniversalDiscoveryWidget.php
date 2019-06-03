<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Exception\ElementNotFoundException;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class UniversalDiscoveryWidget extends Element
{
    public const ELEMENT_NAME = 'UDW';
    private const UDW_LONG_TIMEOUT = 20;
    private const UDW_SHORT_TIMEOUT = 2;
    private const UDW_BRANCH_LOADING_TIMEOUT = 10;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'tabSelector' => '.c-tab-nav-item',
            'mainWindow' => '.m-ud',
            'confirmButton' => '.m-ud__action--confirm',
            'cancelButton' => '.m-ud__action--cancel',
            'selectContentButton' => '.c-select-content-button',
            'elementSelector' => '.c-finder-tree-branch:nth-of-type(%d) .c-finder-tree-leaf',
            'branchLoadingSelector' => '.c-finder-tree-leaf--loading',
            'previewName' => '.c-meta-preview__name',
            'treeBranch' => '.c-finder-tree-branch:nth-child(%d)',
        ];
    }

    /**
     * @param string $itemPath
     */
    public function selectContent(string $itemPath): void
    {
        $pathParts = explode('/', $itemPath);
        $depth = 0;
        foreach ($pathParts as $part) {
            ++$depth;

            $this->context->waitUntilElementIsVisible(sprintf($this->fields['treeBranch'], $depth), self::UDW_LONG_TIMEOUT);
            $this->context->getElementByText($part, sprintf($this->fields['elementSelector'], $depth))->click();
            $this->context->waitUntilElementDisappears($this->fields['branchLoadingSelector'], self::UDW_BRANCH_LOADING_TIMEOUT);
        }
        $expectedContentName = $pathParts[count($pathParts) - 1];
        $this->context->waitUntil(
            self::UDW_LONG_TIMEOUT,
            function () use ($expectedContentName) {
                return $this->context->findElement($this->fields['previewName'])->getText() === $expectedContentName;
            });

        if ($this->isMultiSelect()) {
            for ($i = 0; $i < 3; ++$i) {
                try {
                    $itemToSelect = $this->context->getElementByText($expectedContentName, sprintf($this->fields['elementSelector'], $depth));

                    $this->context->findElement($this->fields['tabSelector'])->mouseOver();
                    $itemToSelect->mouseOver();
                    $this->context->waitUntilElementIsVisible($this->fields['selectContentButton'], $this->defaultTimeout, $itemToSelect);
                    $this->context->findElement($this->fields['selectContentButton'], $this->defaultTimeout, $itemToSelect)->click();
                    break;
                } catch (\Exception $e) {
                    if ($i === 2) {
                        throw $e;
                    }
                }
            }
        }
    }

    public function confirm(): void
    {
        $this->context->getElementByText('Confirm', $this->fields['confirmButton'])->click();
        $this->context->waitUntil(self::UDW_SHORT_TIMEOUT, function () {
            return !$this->isVisible();
        });
    }

    public function cancel(): void
    {
        $this->context->getElementByText('Cancel', $this->fields['cancelButton'])->click();
        $this->context->waitUntil(self::UDW_SHORT_TIMEOUT, function () {
            return !$this->isVisible();
        });
    }

    public function verifyVisibility(): void
    {
        $this->assertExpectedTabsExist();
    }

    private function assertExpectedTabsExist(): void
    {
        $expectedTabTitles = ['Browse', 'Search'];

        $actualTabTitles = [];
        $tabs = $this->context->findAllElements($this->fields['tabSelector']);
        foreach ($tabs as $tab) {
            $actualTabTitles[] = $tab->getText();
        }

        Assert::assertArraySubset($expectedTabTitles, $actualTabTitles);
    }

    protected function isVisible(): bool
    {
        return $this->context->isElementVisible($this->fields['mainWindow'], self::UDW_SHORT_TIMEOUT);
    }

    protected function isMultiSelect(): bool
    {
        try {
            return $this->context->findElement($this->fields['selectContentButton'], self::UDW_SHORT_TIMEOUT) !== null;
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }
}
