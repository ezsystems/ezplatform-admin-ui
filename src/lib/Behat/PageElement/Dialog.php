<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

/** Element that describes dialog popup */
class Dialog extends Element
{
    protected $fields = [
        'confirm' => '.btn--trigger',
        'decline' => '.btn--no',
    ];
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Dialog';

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
        // TODO: Implement verifyVisibility() method. Not sure if it's needed
    }
}
