<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Form\Type\FieldDefinition\User\PasswordConstraintCheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Maps a user FieldType.
 */
final class UserAccountFormMapper implements FieldDefinitionFormMapperInterface
{
    /**
     * @inheritdoc
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $validatorPropertyPathPrefix = 'validatorConfiguration[PasswordValueValidator]';

        $fieldDefinitionForm->add('requireAtLeastOneUpperCaseCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireAtLeastOneUpperCaseCharacter]',
            'label' => /** @Desc("Password must contain at least one uppercase letter") */ 'field_definition.ezuser.require_at_least_one_upper_case_character',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneLowerCaseCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireAtLeastOneLowerCaseCharacter]',
            'label' => /** @Desc("Password must contain at least one lowercase letter") */ 'field_definition.ezuser.require_at_least_one_lower_case_character',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneNumericCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireAtLeastOneNumericCharacter]',
            'label' => /** @Desc("Password must contain at least one number") */ 'field_definition.ezuser.require_at_least_one_numeric_character',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneNonAlphanumericCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireAtLeastOneNonAlphanumericCharacter]',
            'label' => /** @Desc("Password must contain at least one non-alphanumeric character") */ 'field_definition.ezuser.require_at_least_one_non_alphanumeric_character',
        ]);

        $fieldDefinitionForm->add('requireNewPassword', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireNewPassword]',
            'label' => /** @Desc("Prevent reusing old password") */ 'field_definition.ezuser.require_new_password',
        ]);

        $fieldDefinitionForm->add('minLength', IntegerType::class, [
            'required' => false,
            'property_path' => $validatorPropertyPathPrefix . '[minLength]',
            'label' => /** @Desc("Minimum password length") */ 'field_definition.ezuser.min_length',
            'constraints' => [
                new Range(['min' => 0, 'max' => 255]),
            ],
        ]);

        $fieldDefinitionForm->add('passwordTTL', IntegerType::class, [
            'required' => false,
            'property_path' => 'fieldSettings[PasswordTTL]',
            'label' => /** @Desc("Days before password expires") */ 'field_definition.ezuser.password_ttl',
            'constraints' => [
                new Range(['min' => 0, 'max' => null]),
            ],
        ]);

        $fieldDefinitionForm->add('passwordTTLWarning', IntegerType::class, [
            'required' => false,
            'property_path' => 'fieldSettings[PasswordTTLWarning]',
            'label' => /** @Desc("Days before a user is notified about expiration") */ 'field_definition.ezuser.password_ttl_warning',
            'constraints' => [
                new Range(['min' => 0, 'max' => null]),
            ],
        ]);

        $fieldDefinitionForm->add('RequireUniqueEmail', CheckboxType::class, [
            'required' => false,
            'property_path' => 'fieldSettings[RequireUniqueEmail]',
            'label' => /** @Desc("Email must be unique") */ 'field_definition.ezuser.require_unique_email',
        ]);

        $fieldDefinitionForm->add('UsernamePattern', TextType::class, [
            'property_path' => 'fieldSettings[UsernamePattern]',
            'label' => /** @Desc("Username pattern") */ 'field_definition.ezuser.username_pattern',
        ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }
}
