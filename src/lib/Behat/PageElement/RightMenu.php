<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

/** Element that describes right action menu (Create, Preview, Publish etc.) */
class RightMenu extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Right Menu';

    /**
     * Clicks a button on the right menu.
     *
     * @param $buttonName
     */
    public function clickButton(string $buttonName): void
    {
        $this->context->getElementByText($buttonName, '.ez-context-menu .btn-block')->click();
    }

    public function verifyVisibility(): void
    {
        // TODO: Implement verifyVisibility() method.
    }
}
