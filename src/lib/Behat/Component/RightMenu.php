<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\CSSLocator;

class RightMenu extends Component
{
    public function clickButton(string $buttonName): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('menuButton'))
            ->assert()->hasElements()
            ->getByCriterion(new ElementTextCriterion($buttonName))
            ->click();
    }

    public function isButtonActive(string $buttonName): bool
    {
        return !$this->getHTMLPage()->findAll($this->getLocator('menuButton'))->getByCriterion(new ElementTextCriterion($buttonName))->hasAttribute('disabled');
    }

    public function isButtonVisible(string $buttonName): bool
    {
        return $this->getHTMLPage()
            ->findAll($this->getLocator('menuButton'))
            ->filterBy(new ElementTextCriterion($buttonName))
            ->any();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(5)
            ->find($this->getLocator('menuButton'))
            ->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new CSSLocator('menuButton', '.ez-context-menu .btn'),
        ];
    }
}
