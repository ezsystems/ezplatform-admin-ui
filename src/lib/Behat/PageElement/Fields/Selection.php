<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class Selection extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Selection';

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['selectBar'] = '.ez-data-source__selected';
        $this->fields['selectOption'] = '.ez-data-source__options .option-item';
        $this->fields['specificOption'] = '[data-value="%s"]';
    }

    public function setValue(array $parameters): void
    {
        $this->context->findElement(sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectBar']))->click();

        $index = $this->context->getElementPositionByText($parameters['value'], $this->fields['selectOption']) - 2;

        $this->context->findElement(sprintf($this->fields['specificOption'], $index))->click();
    }

    public function getValue(): array
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectBar'])
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
