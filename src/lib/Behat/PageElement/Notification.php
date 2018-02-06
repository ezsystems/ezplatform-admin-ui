<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

class Notification extends Element
{
    protected $fields = [
        'alert' => '.alert',
        'successAlert' => '.alert-success',
    ];
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Notification';

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['alert'], $this->defaultTimeout);
    }

    public function verifyAlertSuccess(): void
    {
        $this->context->assertElementOnPage($this->fields['successAlert']);
    }

    public function getMessage(): string
    {
        return $this->context->findElement($this->fields['alert'])->getText();
    }
}
