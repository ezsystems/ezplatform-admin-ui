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
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\IntegerFieldType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezinteger FieldType.
 */
class IntegerFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $fieldDefinition)
    {
        $isTranslation = $fieldDefinition->contentTypeData->languageCode !== $fieldDefinition->contentTypeData->mainLanguageCode;
        $defaultValueForm = $fieldDefinitionForm
            ->getConfig()
            ->getFormFactory()
            ->createBuilder()
            ->create('defaultValue', IntegerFieldType::class, [
                'required' => false,
                'label' => 'field_definition.ezinteger.default_value',
            ])
            ->setAutoInitialize(false)
            ->getForm();

        $fieldDefinitionForm
            ->add(
                'minValue', IntegerType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[IntegerValueValidator][minIntegerValue]',
                    'label' => 'field_definition.ezinteger.min_value',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'maxValue', IntegerType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[IntegerValueValidator][maxIntegerValue]',
                    'label' => 'field_definition.ezinteger.max_value',
                    'disabled' => $isTranslation,
                ]
            )
            ->add($defaultValueForm);
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
                        IntegerFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                            'min' => $validatorConfiguration['IntegerValueValidator']['minIntegerValue'],
                            'max' => $validatorConfiguration['IntegerValueValidator']['maxIntegerValue'],
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
