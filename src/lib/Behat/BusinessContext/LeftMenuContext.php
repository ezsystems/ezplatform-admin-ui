<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;

class LeftMenuContext extends BusinessContext
{
    /**
     * @When I click on the left menu bar button :buttonName
     */
    public function iClickLeftMenuBarButton(string $buttonName): void
    {
        $leftMenu = ElementFactory::createElement($this->browserContext, LeftMenu::ELEMENT_NAME);
        $leftMenu->clickButton($buttonName);
    }
}
