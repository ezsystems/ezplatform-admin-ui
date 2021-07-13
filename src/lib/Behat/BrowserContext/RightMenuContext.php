<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Component\RightMenu;
use PHPUnit\Framework\Assert;

class RightMenuContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Component\RightMenu */
    private $rightMenu;

    public function __construct(RightMenu $rightMenu)
    {
        $this->rightMenu = $rightMenu;
    }

    /**
     * @Given I click (on) the edit action bar button :button
     */
    public function clickEditActionBar(string $button): void
    {
        $this->rightMenu->clickButton($button);
    }

    /**
     * @Given the buttons are disabled
     */
    public function theButtonsAreDisabled(TableNode $buttons): void
    {
        foreach ($buttons->getHash() as $button) {
            Assert::assertFalse($this->rightMenu->isButtonActive($button['buttonName']));
        }
    }

    /**
     * @Given the :buttonName button is not visible
     */
    public function buttonIsNotVisible(string $buttonName): void
    {
        Assert::assertFalse($this->rightMenu->isButtonVisible($buttonName));
    }
}
