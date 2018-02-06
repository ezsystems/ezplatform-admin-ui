<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

/** Element that describes upper menu (Content, Admin, Page and theirs children) */
class UpperMenu extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Upper Menu';

    /**
     * Clicks on top menu, for example "Content" tab.
     *
     * @param $tabName
     */
    public function goToTab(string $tabName): void
    {
        $this->context->getElementByText($tabName, '.nav-link')->click();
    }

    /**
     * Clicks on expanded submenu, for example "Content Structure" in "Content" section.
     *
     * @param $tabName
     */
    public function goToSubTab(string $tabName): void
    {
        $this->context->getElementByText($tabName, '.navbar-expand-lg .nav-link')->click();
    }

    public function verifyVisibility(): void
    {
        // TODO: Implement verifyVisibility() method.
    }
}
