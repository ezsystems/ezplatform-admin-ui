<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class Notification extends Component
{
    public function verifyAlertSuccess(): void
    {
        $this->getHTMLPage()
            ->setTimeout(20)
            ->find($this->getLocator('successAlert'))
            ->assert()
            ->isVisible();
    }

    public function verifyAlertFailure(): void
    {
        Assert::assertTrue(
            $this->getHTMLPage()
                ->setTimeout(20)
                ->find($this->getLocator('failureAlert'))
                ->isVisible(),
            'Failure alert not found.'
        );
    }

    public function getMessage(): string
    {
        return $this->getHTMLPage()->setTimeout(20)->find($this->getLocator('alertMessage'))->getText();
    }

    public function closeAlert(): void
    {
        $closeButtons = $this->getHTMLPage()->findAll($this->getLocator('closeAlert'));

        foreach ($closeButtons as $closeButton) {
            $closeButton->click();
        }
    }

    public function isVisible(): bool
    {
        return $this->getHTMLPage()->findAll($this->getLocator('alert'))->any();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(20)
            ->find($this->getLocator('alert'))
            ->assert()->isVisible();
    }

    public function verifyMessage(string $expectedMessage)
    {
        $this->getHTMLPage()->find($this->getLocator('alertMessage'))->assert()->textEquals($expectedMessage);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('alert', '.ez-notifications-container .alert.show'),
            new VisibleCSSLocator('alertMessage', '.ez-notifications-container .alert.show span:nth-of-type(2)'),
            new VisibleCSSLocator('successAlert', '.alert-success'),
            new VisibleCSSLocator('failureAlert', '.alert-danger'),
            new VisibleCSSLocator('closeAlert', 'button.close'),
        ];
    }
}
