<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

/** Element that describes dialog popup */
class Dialog extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Dialog';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'confirm' => '.btn--trigger',
            'decline' => '.btn--no',
        ];
    }

    public function confirm(): void
    {
        $this->context->findElement($this->fields['confirm'])->click();
    }

    public function decline(): void
    {
        $this->context->findElement($this->fields['decline'])->click();
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['confirm']);
        $this->context->waitUntilElementIsVisible($this->fields['decline']);
    }
}
