<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;

/** Element that describes right action menu (Create, Preview, Publish etc.) */
class RightMenu extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Right Menu';

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'menuButton' => '.ez-context-menu .btn-block',
        ];
    }

    /**
     * Clicks a button on the right menu.
     *
     * @param $buttonName
     */
    public function clickButton(string $buttonName): void
    {
        $this->context->getElementByText($buttonName, $this->fields['menuButton'])->click();
    }

    public function isButtonActive(string $buttonName): bool
    {
        return !$this->context->getElementByText($buttonName, $this->fields['menuButton'])->hasAttribute('disabled');
    }

    public function isButtonVisible(string $buttonName): bool
    {
        return $this->context->getElementByText($buttonName, $this->fields['menuButton']) !== null;
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['menuButton']);
    }
}
