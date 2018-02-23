<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

/** Element that describes upper menu (Content, Admin, Page and theirs children) */
class UpperMenu extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Upper Menu';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'menuButton' => '.nav-link',
            'submenuButton' => '.navbar-expand-lg .nav-link',
        ];
    }

    /**
     * Clicks on top menu, for example "Content" tab.
     *
     * @param $tabName
     */
    public function goToTab(string $tabName): void
    {
        $this->context->getElementByText($tabName, $this->fields['menuButton'])->click();
    }

    /**
     * Clicks on expanded submenu, for example "Content Structure" in "Content" section.
     *
     * @param $tabName
     */
    public function goToSubTab(string $tabName): void
    {
        $this->context->getElementByText($tabName, $this->fields['submenuButton'])->click();
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['menuButton']);
    }
}
