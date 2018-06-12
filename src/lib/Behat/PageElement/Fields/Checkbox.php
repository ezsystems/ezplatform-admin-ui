<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class Checkbox extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Checkbox';

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['fieldInput'] = '.ez-data-source__indicator';
        $this->fields['checkbox'] = '.ez-data-source__label';
        $this->fields['checked'] = '.is-checked';
    }

    public function setValue(array $parameters): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        if ($this->getValue() !== $parameters['value']) {
            $fieldInput->click();
        }
    }

    public function getValue(): array
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        return [
            filter_var(
                $this->context->findElement(
                    sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['checkbox'])
                )->hasClass($this->fields['checked']),
                FILTER_VALIDATE_BOOLEAN
            ),
        ];
    }

    public function verifyValueInItemView(array $values): void
    {
        Assert::assertEquals(
            filter_var($values['value'], FILTER_VALIDATE_BOOLEAN),
            filter_var($this->context->findElement($this->fields['fieldContainer'])->getText(), FILTER_VALIDATE_BOOLEAN),
            'Field has wrong value'
        );
    }
}
