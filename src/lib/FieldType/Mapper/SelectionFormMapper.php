<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\MultilingualSelectionTransformer;
use EzSystems\EzPlatformAdminUi\Form\EventListener\SelectionMultilingualOptionsDataListener;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectionFormMapper implements FieldDefinitionFormMapperInterface
{
    /**
     * Selection items can be added and removed, the collection field type is used for this.
     * - An empty field is always present, if this is filled it will become a new entry.
     * - If a filled field is cleared the entry will be removed.
     * - Only one new entry can be added per page load (while any number can be removed).
     *   This can be improved using a template override with javascript code.
     * - The prototype_name option is for the empty field which is used for new items. If not
     *   using javascript, it must be unique.
     * - Data for 'options' field can now be supplied either by `options` property_path or by
     *   `multilingualOptions` if those are provided.
     *   `multilingualOptions` is an array with keys equal to used languageCodes.
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $options = $fieldDefinitionForm->getConfig()->getOptions();
        $languageCode = $options['languageCode'];
        $isMultilingual = isset($data->fieldDefinition->fieldSettings['multilingualOptions']);
        $dataPropertyPathName = $isMultilingual ? 'multilingualOptions' : 'options';

        $fieldDefinitionForm
            ->add('isMultiple', CheckboxType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[isMultiple]',
                'label' => /** @Desc("Multiple choice") */ 'field_definition.ezselection.is_multiple',
                'disabled' => $isTranslation,
            ]);

        $formBuilder = $fieldDefinitionForm->getConfig()->getFormFactory()->createBuilder();

        $optionField = $formBuilder->create('options', CollectionType::class, [
            'entry_type' => TextType::class,
            'entry_options' => ['required' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => false,
            'prototype' => true,
            'prototype_name' => '__number__',
            'required' => false,
            'property_path' => 'fieldSettings[' . $dataPropertyPathName . ']',
            'label' => /** @Desc("Options") */ 'field_definition.ezselection.options',
        ]);

        if ($isMultilingual) {
            $dataListener = new SelectionMultilingualOptionsDataListener($languageCode);
            $dataTransformer = new MultilingualSelectionTransformer($languageCode, $data);

            $optionField
                ->addEventListener(
                    FormEvents::PRE_SET_DATA,
                    [$dataListener, 'setLanguageOptions'],
                    10
                )
                ->addModelTransformer(
                    $dataTransformer
                );
        }

        $fieldDefinitionForm->add(
            $optionField
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
                'translation_domain' => 'content_type',
            ]);
    }
}
