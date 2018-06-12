<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class UserAccount extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'User account';

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['username'] = '#ezrepoforms_user_create_fieldsData_field_value_username';
        $this->fields['password'] = '#ezrepoforms_user_create_fieldsData_field_value_password_first';
        $this->fields['confirmPassword'] = '#ezrepoforms_user_create_fieldsData_field_value_password_second';
        $this->fields['email'] = '#ezrepoforms_user_create_fieldsData_field_value_email';
    }

    public function setValue(array $parameters): void
    {
        $this->setSpecificFieldValue('username', $parameters['username']);
        $this->setSpecificFieldValue('password', $parameters['password']);
        $this->setSpecificFieldValue('confirmPassword', $parameters['password']);
        $this->setSpecificFieldValue('email', $parameters['email']);
    }

    public function setSpecificFieldValue(string $fieldName, string $value): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields[$fieldName])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input %s for field %s not found.', $fieldName, $this->label));

        $fieldInput->setValue('');
        $fieldInput->setValue($value);
    }

    public function getValue(): array
    {
        return [
            'username' => $this->getSpecificFieldValue('username'),
            'email' => $this->getSpecificFieldValue('email'),
        ];
    }

    public function getSpecificFieldValue(string $fieldName): string
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields[$fieldName])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input $s for field %s not found.', $fieldName, $this->label));

        return $fieldInput->getValue();
    }

    public function verifyValue(array $value): void
    {
        Assert::assertEquals(
            $value['username'],
            $this->getValue()['username'],
            sprintf('Field %s has wrong value', $value['label'])
        );
        Assert::assertEquals(
            $value['email'],
            $this->getValue()['email'],
            sprintf('Field %s has wrong value', $value['label'])
        );
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
