<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use PHPUnit\Framework\Assert;

/** Context for actions on right menu */
class RightMenuContext extends BusinessContext
{
    /**
     * @Given I click (on) the edit action bar button :button
     * Click on a AdminUI edit action bar
     */
    public function clickEditActionBar(string $button): void
    {
        $rightMenu = new RightMenu($this->browserContext);
        $rightMenu->clickButton($button);
    }

    /**
     * @Given the buttons are disabled
     */
    public function theButtonsAreDisabled(TableNode $buttons): void
    {
        $rightMenu = new RightMenu($this->browserContext);
        foreach ($buttons->getHash() as $button) {
            Assert::assertFalse($rightMenu->isButtonActive($button['buttonName']));
        }
    }

    /**
     * @Given the :buttonName button is not visible
     */
    public function buttonIsNotVisible(string $buttonName): void
    {
        $rightMenu = new RightMenu($this->browserContext);
        Assert::assertFalse($rightMenu->isButtonVisible($buttonName));
    }
}
