<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class Checkbox extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('fieldInput'))
            ->build();

        $newValue = ($parameters['value'] === 'true');

        if ($this->getValue() !== $newValue) {
            $this->getHTMLPage()->find($fieldSelector)->click();
        }
    }

    public function getValue(): array
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('fieldInput'))
            ->build();

        return [
            $this->getHTMLPage()->find($fieldSelector)->hasClass($this->getLocator('checked')->getSelector()),
        ];
    }

    public function verifyValueInItemView(array $values): void
    {
        $expectedValue = $values['value'] === 'true' ? 'Yes' : 'No';

        Assert::assertEquals(
            $expectedValue,
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Field has wrong value'
        );
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezboolean';
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', '.ibexa-toggle__indicator'),
            new VisibleCSSLocator('checkbox', '.ibexa-toggle__switcher'),
            new VisibleCSSLocator('checked', 'ibexa-toggle--is-checked'),
        ];
    }
}
