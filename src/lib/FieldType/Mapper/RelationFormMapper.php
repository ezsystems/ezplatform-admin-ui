<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Form\Type\LocationType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationFormMapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('selectionRoot', LocationType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[selectionRoot]',
                'label' => /** @Desc("Add field definition") */ 'field_definition.ezobjectrelation.selection_root',
            ])
            ->add('selectionContentTypes', ChoiceType::class, [
                'choices' => $this->getContentTypesHash(),
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'property_path' => 'fieldSettings[selectionContentTypes]',
                'label' => /** @Desc("Allowed content types") */ 'field_definition.ezobjectrelation.selection_content_types',
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
