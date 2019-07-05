<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;

class NavLinkTabs extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'NavLinkTabs';

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'activeNavLink' => '.ez-tabs .active',
            'navLink' => '.ez-tabs .nav-link',
        ];
    }

    public function verifyVisibility(): void
    {
        $this->context->findElement($this->fields['activeNavLink']);
    }

    public function getActiveTabName(): string
    {
        return $this->context->findElement($this->fields['activeNavLink'])->getText();
    }

    public function goToTab(string $tabName): void
    {
        if ($tabName !== $this->getActiveTabName()) {
            $this->context->getElementByTextFragment($tabName, $this->fields['navLink'])->click();
        }
    }
}
