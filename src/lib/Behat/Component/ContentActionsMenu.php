<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class ContentActionsMenu extends Component
{
    public function clickButton(string $buttonName): void
    {
        $buttons = $this->getHTMLPage()
            ->findAll($this->getLocator('menuButtonLabel'))
            ->filterBy(new ElementTextCriterion($buttonName));

        if ($buttons->any()) {
            $buttons->first()->click();

            return;
        }

        $moreButton = $this->getHTMLPage()->find($this->getLocator('moreButton'))->click();

        $this->getHTMLPage()
            ->findAll($this->getLocator('expandedMenuButton'))
            ->getByCriterion(new ElementTextCriterion($buttonName))
            ->click();
    }

    public function isButtonActive(string $buttonName): bool
    {
        $moreButton = $this->getHTMLPage()->findAll($this->getLocator('moreButton'));
        if ($moreButton->any()) {
            $moreButton->single()->click();
        }

        return !$this->getHTMLPage()->findAll($this->getLocator('menuButton'))->getByCriterion(new ElementTextCriterion($buttonName))->hasAttribute('disabled');
    }

    public function isButtonVisible(string $buttonName): bool
    {
        $moreButton = $this->getHTMLPage()->findAll($this->getLocator('moreButton'));
        if ($moreButton->any()) {
            $moreButton->single()->click();
        }

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
            new VisibleCSSLocator('menuButton', '.ibexa-context-menu .ibexa-btn, .ibexa-context-menu__item .ibexa-popup-menu__item, .ez-context-menu .btn'), // TO DO: set one selector after redesign
            new VisibleCSSLocator('menuButtonLabel', '.ibexa-context-menu .ibexa-btn .ibexa-btn__label, .ibexa-context-menu__item .ibexa-popup-menu__item, .ez-context-menu .btn'), // TO DO: set one selector after redesign
            new VisibleCSSLocator('moreButton', '.ibexa-context-menu__item--more'),
            new VisibleCSSLocator('expandedMenuButton', '.ibexa-context-menu__item .ibexa-popup-menu__item'),
        ];
    }
}
