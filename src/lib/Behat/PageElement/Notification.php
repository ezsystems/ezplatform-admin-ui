<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

/** Element that describes user notification bar, that appears on the bottom of the screen */
class Notification extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Notification';

    private $checkVisibilityTimeout;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'alert' => '.alert',
            'successAlert' => '.alert-success',
            'failureAlert' => '.alert-danger',
            'closeAlert' => '.alert.show button.close',
        ];
        $this->checkVisibilityTimeout = 1;
    }

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

    public function closeAlert(): void
    {
        $this->context->findElement($this->fields['closeAlert'])->click();

        $this->context->waitUntil($this->defaultTimeout, function () {
            return !$this->isVisible();
        });
    }

    public function isVisible(): bool
    {
        return $this->context->isElementVisible($this->fields['alert'], $this->checkVisibilityTimeout);
    }
}
