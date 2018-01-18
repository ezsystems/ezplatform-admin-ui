<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat;


use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;

class ContentContext extends BusinessContext
{
    /**
     * @Given I click (on) the edit action bar button :button
     * Click on a PlatformUI edit action bar
     *
     * @param  string   $button     Text of the element to click
     */
    public function clickEditActionBar($button)
    {
        $rightMenu = new RightMenu($this->utilityContext);
        $rightMenu->clickButton($button);
    }

    /**
     * @Given I (can) create another content (in Platform)
     */
    public function canCreateAnotherContent()
    {
        $this->clickEditActionBar('Create');
    }

}