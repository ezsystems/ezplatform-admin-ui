<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class Authors extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Authors';

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['nameFieldInput'] = '.ez-data-source__field--name input';
        $this->fields['emailFieldInput'] = '.ez-data-source__field--email input';
    }

    public function setValue(array $parameters): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['nameFieldInput'])
        );

        $fieldInput->setValue('');
        $fieldInput->setValue($parameters['name']);

        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['emailFieldInput'])
        );

        $fieldInput->setValue('');
        $fieldInput->setValue($parameters['email']);
    }

    public function getValue(): array
    {
        $nameInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['nameFieldInput'])
        );

        $emailInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['emailFieldInput'])
        );

        return ['name' => $nameInput->getValue(), 'email' => $emailInput->getValue()];
    }

    public function verifyValue(array $value): void
    {
        $actualFieldValues = $this->getValue();
        Assert::assertEquals(
            $value['name'],
            $actualFieldValues['name'],
            sprintf('Field %s has wrong value', $value['label'])
        );

        Assert::assertEquals(
            $value['name'],
            $actualFieldValues['name'],
            sprintf('Field %s has wrong value', $value['label'])
        );
    }
}
