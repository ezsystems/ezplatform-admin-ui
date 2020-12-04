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
        $this->fields['firstname'] = '#ezrepoforms_user_create_fieldsData_first_name_value,#ezrepoforms_user_update_fieldsData_first_name_value';
        $this->fields['lastname'] = '#ezrepoforms_user_create_fieldsData_last_name_value,#ezrepoforms_user_update_fieldsData_last_name_value';
        $this->fields['username'] = '#ezrepoforms_user_create_fieldsData_user_account_value_username,#ezrepoforms_user_update_fieldsData_user_account_value_username';
        $this->fields['password'] = '#ezrepoforms_user_create_fieldsData_user_account_value_password_first,#ezrepoforms_user_update_fieldsData_user_account_value_password_first';
        $this->fields['confirmPassword'] = '#ezrepoforms_user_create_fieldsData_user_account_value_password_second,#ezrepoforms_user_update_fieldsData_user_account_value_password_second';
        $this->fields['email'] = '#ezrepoforms_user_create_fieldsData_user_account_value_email,#ezrepoforms_user_update_fieldsData_user_account_value_email';
        $this->fields['buttonEnabled'] = '#ezrepoforms_user_create_fieldsData_user_account_value_enabled,#ezrepoforms_user_update_fieldsData_user_account_value_enabled';
    }

    public function setValue(array $parameters): void
    {
        $this->setSpecificFieldValue('username', $parameters['Username']);
        $this->setSpecificFieldValue('password', $parameters['Password']);
        $this->setSpecificFieldValue('confirmPassword', $parameters['Confirm password']);
        $this->setSpecificFieldValue('email', $parameters['Email']);
        $this->setEnabledField(true);
    }

    private function setEnabledField(bool $enabled)
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['buttonEnabled']));
        $isCurrentlyEnabled = $fieldInput->hasClass('is-checked');
        if ($isCurrentlyEnabled !== $enabled) {
            $fieldInput->click();
        }
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
        $actualUsername = $this->context->findElement(sprintf('%s %s', $this->fields['fieldContainer'], 'tr:nth-of-type(1) td:nth-of-type(2)'))->getText();
        $actualEmail = $this->context->findElement(sprintf('%s %s', $this->fields['fieldContainer'], 'tr:nth-of-type(2) td:nth-of-type(2)'))->getText();
        $actualEnabled = $this->context->findElement(sprintf('%s %s', $this->fields['fieldContainer'], 'tr:nth-of-type(3) td:nth-of-type(2)'))->getText();

        Assert::assertEquals($values['Username'], $actualUsername, sprintf('Expected: %s Actual: %s', $values['Username'], $actualUsername));
        Assert::assertEquals($values['Email'], $actualEmail, sprintf('Expected: %s Actual: %s', $values['Email'], $actualEmail));
        Assert::assertEquals($values['Enabled'], $actualEnabled, sprintf('Expected: %s Actual: %s', $values['Enabled'], $actualEnabled));
    }
}
