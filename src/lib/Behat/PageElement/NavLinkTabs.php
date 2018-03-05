<?php
/**
 * Created by PhpStorm.
 * User: maciejtyrala
 * Date: 22/02/2018
 * Time: 13:26
 */

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;


use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class NavLinkTabs extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'NavLinkTabs';

    public function __construct(UtilityContext $context)
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
        if($tabName !== $this->getActiveTabName()){
            $this->context->getElementByTextFragment($tabName,$this->fields['navLink'])->click();
        }
    }
}