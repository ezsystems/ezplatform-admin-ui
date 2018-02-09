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
    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'tabSelector' => '.c-tab-nav-item',
            'confirmButton' => '.m-ud__action--confirm',
            'selectContentButton' => '.c-meta-preview__btn--select',
            'elementSelector' => '.c-finder-tree-branch:nth-of-type(%d) .c-finder-tree-leaf',
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
            $this->context->getElementByText($part, sprintf($this->fields['elementSelector'], $depth))->click();
            ++$depth;
        }

        $this->context->findElement($this->fields['selectContentButton'])->click();
    }

    public function confirm(): void
    {
        $this->context->getElementByText('Confirm', $this->fields['confirmButton'])->click();
    }

    public function verifyVisibility(): void
    {
        $this->assertExpectedTabsExist();
    }

    private function assertExpectedTabsExist(): void
    {
        $expectedTabTitles = ['Browse', 'Search'];

        $actualTabTitles = [];
        $tabs = $this->context->findAllWithWait($this->fields['tabSelector']);
        foreach ($tabs as $tab) {
            $actualTabTitles[] = $tab->getText();
        }

        Assert::assertArraySubset($expectedTabTitles, $actualTabTitles);
    }
}
