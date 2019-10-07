<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\RelationFieldType;
use EzSystems\EzPlatformAdminUi\Form\Type\LocationType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationFormMapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('selectionRoot', LocationType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[selectionRoot]',
                'label' => 'field_definition.ezobjectrelation.selection_root',
            ])
            ->add('selectionContentTypes', ChoiceType::class, [
                'choices' => $this->getContentTypesHash(),
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'property_path' => 'fieldSettings[selectionContentTypes]',
                'label' => 'field_definition.ezobjectrelation.selection_content_types',
                'disabled' => $isTranslation,
            ]);
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', RelationFieldType::class, [
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName(),
                        'default_location' => $this->loadDefaultLocationForSelection(
                            $fieldDefinition->getFieldSettings()['selectionRoot']
                        ),
                    ])
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * Fake method to set the translation domain for the extractor.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
