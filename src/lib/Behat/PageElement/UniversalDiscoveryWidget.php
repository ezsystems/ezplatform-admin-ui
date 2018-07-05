<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class UniversalDiscoveryWidget extends Element
{
    public const ELEMENT_NAME = 'UDW';
    private const UDW_TIMEOUT = 20;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'tabSelector' => '.c-tab-nav-item',
            'mainWindow' => '.m-ud',
            'confirmButton' => '.m-ud__action--confirm',
            'cancelButton' => '.m-ud__action--cancel',
            'selectContentButton' => '.c-meta-preview__btn--select',
            'elementSelector' => '.c-finder-tree-branch:nth-of-type(%d) .c-finder-tree-leaf',
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
        $depth = 1;
        foreach ($pathParts as $part) {
            $this->context->waitUntilElementIsVisible(sprintf($this->fields['treeBranch'], $depth), self::UDW_TIMEOUT);
            $this->context->getElementByText($part, sprintf($this->fields['elementSelector'], $depth))->click();
            ++$depth;
        }
        $expectedContentName = $pathParts[count($pathParts) - 1];
        $this->context->waitUntil(
            self::UDW_TIMEOUT,
            function () use ($expectedContentName) {
                return $this->context->findElement($this->fields['previewName'])->getText() === $expectedContentName;
            });

        $this->context->findElement($this->fields['selectContentButton'])->click();
    }

    public function confirm(): void
    {
        $this->context->getElementByText('Confirm', $this->fields['confirmButton'])->click();
        $this->context->waitUntil($this->defaultTimeout, function () {
            return !$this->isVisible();
        });
    }

    public function cancel(): void
    {
        $this->context->getElementByText('Cancel', $this->fields['cancelButton'])->click();
        $this->context->waitUntil($this->defaultTimeout, function () {
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
        return $this->context->isElementVisible($this->fields['mainWindow']);
    }
}
