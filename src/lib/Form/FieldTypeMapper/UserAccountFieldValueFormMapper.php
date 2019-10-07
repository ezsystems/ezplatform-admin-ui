<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\User\Value as ApiUserValue;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTranslationData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\User\UserAccountFieldData;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\FieldType\FieldValueFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldDefinition\User\PasswordConstraintCheckboxType;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\UserAccountFieldType;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\UserAccountPassword;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Maps a user FieldType.
 */
final class UserAccountFieldValueFormMapper implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
    /**
     * Maps Field form to current FieldType based on the configured form type (self::$formType).
     *
     * @param FormInterface $fieldForm form for the current Field
     * @param FieldData $data underlying data for current Field form
     *
     * @throws AlreadySubmittedException
     * @throws LogicException
     * @throws UnexpectedTypeException
     * @throws InvalidOptionsException
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $rootForm = $fieldForm->getRoot()->getRoot();
        $formIntent = $rootForm->getConfig()->getOption('intent');
        $isTranslation = $rootForm->getData() instanceof ContentTranslationData;
        $formBuilder = $formConfig->getFormFactory()->createBuilder()
            ->create('value', UserAccountFieldType::class, [
                'required' => true,
                'label' => $fieldDefinition->getName(),
                'intent' => $formIntent,
                'constraints' => [
                    new UserAccountPassword(['contentType' => $rootForm->getData()->contentType]),
                ],
            ]);

        if ($isTranslation) {
            $formBuilder->addModelTransformer($this->getModelTransformerForTranslation($fieldDefinition));
        } else {
            $formBuilder->addModelTransformer($this->getModelTransformer());
        }

        $formBuilder->setAutoInitialize(false);

        $fieldForm->add($formBuilder->getForm());
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
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
                'translation_domain' => 'ezrepoforms_content_type',
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
