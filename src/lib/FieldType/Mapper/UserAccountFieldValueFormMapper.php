<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\User\Value as ApiUserValue;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Data\User\UserAccountFieldData;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformContentForms\Form\Type\FieldDefinition\User\PasswordConstraintCheckboxType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Maps a user FieldType.
 */
final class UserAccountFieldValueFormMapper implements FieldDefinitionFormMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $validatorPropertyPathPrefix = 'validatorConfiguration[PasswordValueValidator]';

        $fieldDefinitionForm->add('requireAtLeastOneUpperCaseCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireAtLeastOneUpperCaseCharacter]',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneLowerCaseCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireAtLeastOneLowerCaseCharacter]',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneNumericCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireAtLeastOneNumericCharacter]',
        ]);

        $fieldDefinitionForm->add('requireAtLeastOneNonAlphanumericCharacter', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireAtLeastOneNonAlphanumericCharacter]',
        ]);

        $fieldDefinitionForm->add('requireNewPassword', PasswordConstraintCheckboxType::class, [
            'property_path' => $validatorPropertyPathPrefix . '[requireNewPassword]',
        ]);

        $fieldDefinitionForm->add('minLength', IntegerType::class, [
            'required' => false,
            'property_path' => $validatorPropertyPathPrefix . '[minLength]',
            'label' => 'field_definition.ezuser.min_length',
            'constraints' => [
                new Range(['min' => 0, 'max' => 255]),
            ],
        ]);

        $fieldDefinitionForm->add('passwordTTL', IntegerType::class, [
            'required' => false,
            'property_path' => 'fieldSettings[PasswordTTL]',
            'label' => 'field_definition.ezuser.password_ttl',
            'constraints' => [
                new Range(['min' => 0, 'max' => null]),
            ],
        ]);

        $fieldDefinitionForm->add('passwordTTLWarning', IntegerType::class, [
            'required' => false,
            'property_path' => 'fieldSettings[PasswordTTLWarning]',
            'label' => 'field_definition.ezuser.password_ttl_warning',
            'constraints' => [
                new Range(['min' => 0, 'max' => null]),
            ],
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
                'translation_domain' => 'ezplatform_content_forms_content_type',
            ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     *
     * @return \Symfony\Component\Form\CallbackTransformer
     */
    public function getModelTransformerForTranslation(FieldDefinition $fieldDefinition): CallbackTransformer
    {
        return new CallbackTransformer(
            function (ApiUserValue $data) {
                return new UserAccountFieldData($data->login, null, $data->email, $data->enabled);
            },
            function (UserAccountFieldData $submittedData) use ($fieldDefinition) {
                $userValue = clone $fieldDefinition->defaultValue;
                $userValue->login = $submittedData->username;
                $userValue->email = $submittedData->email;
                $userValue->enabled = $submittedData->enabled;

                return $userValue;
            }
        );
    }

    /**
     * @return \Symfony\Component\Form\CallbackTransformer
     */
    public function getModelTransformer(): CallbackTransformer
    {
        return new CallbackTransformer(
            function (ApiUserValue $data) {
                return new UserAccountFieldData($data->login, null, $data->email, $data->enabled);
            },
            function (UserAccountFieldData $submittedData) {
                return $submittedData;
            }
        );
    }
}
