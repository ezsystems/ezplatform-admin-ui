<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use PHPUnit\Framework\Assert;

class LeftMenu extends Element
{
    private $buttonSelector = '.ez-sticky-container .btn';
    private $menuSelector = 'ez-side-menu';

    /**
     * Clicks a button on the left menu (CSearch, Browse, Trash).
     *
     * @param string $buttonName
     */
    public function clickButton(string $buttonName)
    {
        $this->context->getElementByText($buttonName, $this->buttonSelector)->click();
    }

    public function verifyVisibility(): void
    {
        Assert::assertTrue($this->context->findElement($this->menuSelector)->isVisible());
    }
}
