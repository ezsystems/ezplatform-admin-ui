<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

/** Element that describes user notification bar, that appears on the bottom of the screen */
class Notification extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Notification';

    private $checkVisibilityTimeout;
    private $notificationElement;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'alert' => '.ez-notifications-container .alert.show',
            'successAlert' => 'alert-success',
            'failureAlert' => 'alert-danger',
            'closeAlert' => 'button.close',
        ];
        $this->checkVisibilityTimeout = 1;
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntil(10, function () {
            return $this->isVisible();
        });

        $this->setAlertElement();
    }

    public function verifyAlertSuccess(): void
    {
        $this->setAlertElement();

        Assert::assertTrue(
            $this->notificationElement->hasClass($this->fields['successAlert']),
            'Success alert not found.'
        );
    }

    public function verifyAlertFailure(): void
    {
        $this->setAlertElement();

        Assert::assertTrue(
            $this->notificationElement->hasClass($this->fields['failureAlert']),
            'Success alert not found.'
        );
    }

    public function getMessage(): string
    {
        $this->setAlertElement();

        try {
            return $this->notificationElement->getText();
        } catch (\Exception $e) {
            Assert::fail('Notification alert not found, no message can be fetched.');
        }
    }

    public function closeAlert(): void
    {
        if ($this->isVisible()) {
            $this->setAlertElement();

            $this->notificationElement->find('css', $this->fields['closeAlert'])->click();

            $this->context->waitUntil($this->defaultTimeout, function () {
                return !$this->isVisible();
            });
        }
    }

    public function isVisible(): bool
    {
        return $this->context->isElementVisible($this->fields['alert'], $this->checkVisibilityTimeout);
    }

    private function setAlertElement(): void
    {
        if (!isset($this->notificationElement)) {
            $this->notificationElement = $this->context->findElement($this->fields['alert']);
        }
    }
}
