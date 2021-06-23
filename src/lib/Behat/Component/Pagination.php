<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class Pagination extends Component
{
    public function isNextButtonActive(): bool
    {
        return $this->getHTMLPage()->setTimeout(0)->findAll($this->getLocator('nextButton'))->any();
    }

    public function clickNextButton(): void
    {
        $currentPage = (int) $this->getHTMLPage()->find($this->getLocator('currentPage'))->getText();
        $this->getHTMLPage()->find($this->getLocator('nextButton'))->click();
        $this->getHTMLPage()->setTimeout(10)->waitUntil(function () use ($currentPage) {
            $activePge = (int) $this->getHTMLPage()->find($this->getLocator('currentPage'))->getText();

            return $activePge === $currentPage + 1;
        }, 'Next page in pagination was not reloaded in time.');
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('currentPage'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('nextButton', '.pagination .page-item.next:not(.disabled)'),
            new VisibleCSSLocator('currentPage', '.pagination .page-item.active'),
        ];
    }
}
