<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper;

use eZ\Publish\Core\FieldType\Time\Type;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\FieldType\FieldValueFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\TimeFieldType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for eztime FieldType.
 */
class TimeFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add(
                'useSeconds',
                CheckboxType::class,
                [
                    'required' => false,
                    'property_path' => 'fieldSettings[useSeconds]',
                    'label' => 'field_definition.eztime.use_seconds',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'defaultType',
                ChoiceType::class,
                [
                    'choices' => [
                        'field_definition.eztime.default_type_empty' => Type::DEFAULT_EMPTY,
                        'field_definition.eztime.default_type_current' => Type::DEFAULT_CURRENT_TIME,
                    ],
                    'expanded' => true,
                    'required' => true,
                    'property_path' => 'fieldSettings[defaultType]',
                    'label' => 'field_definition.eztime.default_type',
                    'disabled' => $isTranslation,
                ]
            );
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $fieldSettings = $fieldDefinition->getFieldSettings();
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', TimeFieldType::class, [
                        'with_seconds' => $fieldSettings['useSeconds'],
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName(),
                    ])
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
