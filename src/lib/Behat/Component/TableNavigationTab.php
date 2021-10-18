<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class TableNavigationTab extends Component
{
    public function getActiveTabName(): string
    {
        return $this->getHTMLPage()->find($this->getLocator('activeNavLink'))->getText();
    }

    public function goToTab(string $tabName): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('navLink'))
            ->filter(static function (ElementInterface $element) use ($tabName) {
                return strpos($element->getText(), $tabName) !== false;
            })
            ->first()
            ->click();
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('activeNavLink'))->isVisible());
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('activeNavLink', '.ibexa-tabs .active'),
            new VisibleCSSLocator('navLink', '.ibexa-tabs .nav-link'),
        ];
    }
}
