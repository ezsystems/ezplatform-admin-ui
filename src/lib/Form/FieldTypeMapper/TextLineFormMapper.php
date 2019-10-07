<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\FieldType\FieldValueFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\TextLineFieldType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezstring FieldType.
 */
class TextLineFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('minLength', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[StringLengthValidator][minStringLength]',
                'label' => 'field_definition.ezstring.min_length',
                'attr' => ['min' => 0],
                'disabled' => $isTranslation,
            ])
            ->add('maxLength', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[StringLengthValidator][maxStringLength]',
                'label' => 'field_definition.ezstring.max_length',
                'attr' => ['min' => 0],
                'disabled' => $isTranslation,
            ])
            ->add(
                $fieldDefinitionForm
                    ->getConfig()->getFormFactory()->createBuilder()
                    ->create('defaultValue', TextLineFieldType::class, [
                        'required' => false,
                        'label' => 'field_definition.ezstring.default_value',
                        'disabled' => $isTranslation,
                    ])
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        TextLineFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                            'min' => $validatorConfiguration['StringLengthValidator']['minStringLength'],
                            'max' => $validatorConfiguration['StringLengthValidator']['maxStringLength'],
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
