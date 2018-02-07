<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use PHPUnit\Framework\Assert;

class UniversalDiscoveryWidget extends Element
{
    public function selectContent($itemPath)
    {
        $pathParts = explode('/', $itemPath);
        $depth = 1;
        foreach ($pathParts as $part) {
            $this->context->getElementByText($part, ".c-finder-tree-branch:nth-of-type({$depth}) .c-finder-tree-leaf")->click();
            ++$depth;
        }

        $this->context->findElement('.c-meta-preview__btn--select')->click();
    }

    public function confirm()
    {
        $this->context->getElementByText('Confirm', '.m-ud__action--confirm')->click();
    }

    public function verifyVisibility(): void
    {
        $this->assertExpectedTabsExist();
    }

    private function assertExpectedTabsExist(): void
    {
        $expectedTabTitles = ['Browse', 'Search'];

        $tabs = $this->context->findAllWithWait('.c-tab-nav-item');
        foreach ($tabs as $tab) {
            $actualTabTitles[] = $tab->getText();
        }

        Assert::assertArraySubset($expectedTabTitles, $actualTabTitles);
    }
}
