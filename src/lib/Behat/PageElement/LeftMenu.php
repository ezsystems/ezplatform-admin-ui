<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class LeftMenu extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'LeftMenu';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'buttonSelector' => '.ez-sticky-container .btn',
            'menuSelector' => '.ez-side-menu',
        ];
    }

    /**
     * Clicks a button on the left menu (Search, Browse, Trash).
     *
     * @param string $buttonName
     */
    public function clickButton(string $buttonName)
    {
        $this->context->getElementByText($buttonName, $this->fields['buttonSelector'])->click();
    }

    public function verifyVisibility(): void
    {
        Assert::assertTrue($this->context->findElement($this->fields['menuSelector'])->isVisible());
    }
}
