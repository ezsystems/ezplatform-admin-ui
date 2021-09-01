<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class Selection extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $value = $parameters['value'];

        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('selectBar'))
            ->build();

        $this->getHTMLPage()->find($fieldSelector)->click();
        $this->getHTMLPage()->findAll($this->getLocator('selectOption'))->getByCriterion(new ElementTextCriterion($value))->click();
    }

    public function getValue(): array
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('selectBar'))
            ->build();

        return [$this->getHTMLPage()->find($fieldSelector)->getValue()];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezselection';
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('selectBar', '.ibexa-dropdown__selection-info'),
            new VisibleCSSLocator('selectOption', '.ibexa-dropdown__item'),
            new VisibleCSSLocator('specificOption', '.ibexa-dropdown__item:nth-child(%s)'),
        ];
    }
}
