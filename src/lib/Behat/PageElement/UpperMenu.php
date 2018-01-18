<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;


class UpperMenu extends Element
{
    public function goToTab($tabName)
    {
        $this->context->clickElementByText($tabName, '.nav-link');
    }

    public function goToSubTab($tabName)
    {
        $this->context->clickElementByText($tabName, '.navbar-expand-lg .nav-link');
    }
}