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
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\CountryFieldType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add(
                'isMultiple',
                CheckboxType::class, [
                    'required' => false,
                    'property_path' => 'fieldSettings[isMultiple]',
                    'label' => 'field_definition.ezcountry.is_multiple',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                // Creating from FormBuilder as we need to add a DataTransformer.
                $fieldDefinitionForm->getConfig()->getFormFactory()->createBuilder()
                    ->create(
                        'defaultValue',
                        CountryFieldType::class, [
                            'multiple' => true,
                            'expanded' => false,
                            'required' => false,
                            'label' => 'field_definition.ezcountry.default_value',
                            'disabled' => $isTranslation,
                        ]
                    )
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)->getForm()
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
                    ->create('value', CountryFieldType::class, [
                        'multiple' => $fieldSettings['isMultiple'],
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName(),
                    ])
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
