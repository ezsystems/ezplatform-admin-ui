<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class ContentTypePicker extends Component
{
    public function select(string $contentTypeName): void
    {
        $countBeforeFiltering = $this->getDisplayedItemsCount();
        $this->getHTMLPage()->find($this->getLocator('filterInput'))->setValue($contentTypeName);
        $this->getHTMLPage()->setTimeout(3)->waitUntil(function () use ($countBeforeFiltering) {
            return $countBeforeFiltering === 1 || $this->getDisplayedItemsCount() < $countBeforeFiltering;
        }, 'The number of displayed Content Types did not decrease after filtering.');
        $this->getHTMLPage()
            ->findAll($this->getLocator('filteredItem'))
            ->getByCriterion(new ElementTextCriterion($contentTypeName))
            ->click();
    }

    protected function getDisplayedItemsCount(): int
    {
        return $this->getHTMLPage()->findAll($this->getLocator('filteredItem'))->count();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(3)->find($this->getLocator('header'))->assert()->textEquals('Create content');
        $this->getHTMLPage()->find($this->getLocator('filterInput'))->clear();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('filterInput', '.ibexa-extra-actions__section-content--content-type .ez-instant-filter__input'),
            new VisibleCSSLocator('filteredItem', '.ibexa-extra-actions__section-content--content-type .ez-instant-filter__group-item:not([hidden]) .form-check-label'),
            new VisibleCSSLocator('header', '.ibexa-extra-actions--create .ibexa-extra-actions__header h2'),
        ];
    }
}
