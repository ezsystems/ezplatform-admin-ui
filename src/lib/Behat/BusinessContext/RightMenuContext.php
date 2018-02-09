<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;

/** Context for actions on right menu */
class RightMenuContext extends BusinessContext
{
    /**
     * @Given I click (on) the edit action bar button :button
     * Click on a AdminUI edit action bar
     */
    public function clickEditActionBar(string $button): void
    {
        $rightMenu = new RightMenu($this->utilityContext);
        $rightMenu->clickButton($button);
    }
}
