<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

/** Element that describes user notification bar, that appears on the bottom of the screen */
class Notification extends Element
{
    protected $fields = [
        'alert' => '.alert',
        'successAlert' => '.alert-success',
        'failureAlert' => '.alert-danger',
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

    public function verifyAlertFailure(): void
    {
        $this->context->assertElementOnPage($this->fields['failureAlert']);
    }

    public function getMessage(): string
    {
        return $this->context->findElement($this->fields['alert'])->getText();
    }
}
