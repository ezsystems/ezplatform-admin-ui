<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Form\Type\LocationType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationListFormMapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('selectionDefaultLocation', LocationType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[selectionDefaultLocation]',
                'label' => /** @Desc("Default Location") */ 'field_definition.ezobjectrelationlist.selection_default_location',
                'disabled' => $isTranslation,
            ])
            ->add('selectionContentTypes', ChoiceType::class, [
                'choices' => $this->getContentTypesHash(),
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'property_path' => 'fieldSettings[selectionContentTypes]',
                'label' => /** @Desc("Allowed Content Types") */ 'field_definition.ezobjectrelationlist.selection_content_types',
                'disabled' => $isTranslation,
            ])
            ->add('selectionLimit', IntegerType::class, [
                'required' => false,
                'empty_data' => 0,
                'property_path' => 'validatorConfiguration[RelationListValueValidator][selectionLimit]',
                'label' => /** @Desc("Selection limit") */ 'field_definition.ezobjectrelationlist.selection_limit',
                'disabled' => $isTranslation,
            ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }
}
