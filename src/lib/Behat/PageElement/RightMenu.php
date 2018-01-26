<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

class RightMenu extends Element
{
    /**
     * Clicks a button on the right menu (Create, Preview, Publish etc.).
     *
     * @param $buttonName
     */
    public function clickButton($buttonName)
    {
        $this->context->getElementByText($buttonName, '.ez-context-menu .btn-block')->click();
    }
}
