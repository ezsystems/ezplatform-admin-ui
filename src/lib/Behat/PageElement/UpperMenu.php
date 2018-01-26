<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

class UpperMenu extends Element
{
    /**
     * Clicks on top menu, for example "Content" tab.
     *
     * @param $tabName
     */
    public function goToTab($tabName)
    {
        $this->context->getElementByText($tabName, '.nav-link')->click();
    }

    /**
     * Clicks on expanded submenu, for example "Content Structure" in "Content" section.
     *
     * @param $tabName
     */
    public function goToSubTab($tabName)
    {
        $this->context->getElementByText($tabName, '.navbar-expand-lg .nav-link')->click();
    }
}
