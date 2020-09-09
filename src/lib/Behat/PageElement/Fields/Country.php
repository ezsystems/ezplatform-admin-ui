<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Context\BrowserContext;
use PHPUnit\Framework\Assert;

class Country extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Country';

    public function __construct(BrowserContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['fieldInput'] = 'select';
        $this->fields['dropdownSelector'] = '.ez-custom-dropdown__selection-info';
        $this->fields['dropdownExpanded'] = '.ez-custom-dropdown__selection-info:not(.ez-custom-dropdown__items--hidden)';
        $this->fields['dropdownItem'] = '.ez-custom-dropdown__item';
    }

    public function setValue(array $parameters): void
    {
        $this->context->findElement($this->fields['dropdownSelector'])->click();
        $this->context->waitUntilElementIsVisible($this->fields['dropdownExpanded']);
        $this->context->getElementByText($parameters['value'], $this->fields['dropdownItem'])->click();
        $this->context->findElement($this->fields['dropdownSelector'])->click();
    }

    public function getValue(): array
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        return [$fieldInput->getValue()];
    }

    public function verifyValueInItemView(array $values): void
    {
        Assert::assertEquals(
            $values['value'],
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
    }
}
