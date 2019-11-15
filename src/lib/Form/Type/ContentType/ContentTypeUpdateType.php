<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use EzSystems\EzPlatformAdminUi\Form\DataTransformer\TranslatablePropertyTransformer;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldDefinition\FieldDefinitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData;

/**
 * Form type for ContentType update.
 */
class ContentTypeUpdateType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_content_forms_contenttype_update';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => ContentTypeData::class,
                'translation_domain' => 'content_type',
                'mainLanguageCode' => null,
            ])
            ->setDefined(['mainLanguageCode'])
            ->setAllowedTypes('mainLanguageCode', ['null', 'string'])
            ->setRequired(['languageCode']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hasFieldDefinition = count($options['data']->fieldDefinitionsData) > 0;
        $isTranslation = $options['mainLanguageCode'] !== $options['languageCode'];

        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $builder
            ->add(
                $builder
                    ->create('name', TextType::class, [
                        'property_path' => 'names',
                        'label' => /** @Desc("Name") */ 'content_type.name',
                    ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('identifier', TextType::class, [
                'label' => /** @Desc("Identifier") */ 'content_type.identifier',
                'disabled' => $isTranslation,
            ])
            ->add(
                $builder
                    ->create('description', TextType::class, [
                        'property_path' => 'descriptions',
                        'required' => false,
                        'label' => /** @Desc("Description") */ 'content_type.description',
                    ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('nameSchema', TextType::class, [
                'required' => false,
                'label' => /** @Desc("Content name pattern") */ 'content_type.name_schema',
                'disabled' => $isTranslation,
            ])
            ->add('urlAliasSchema', TextType::class, [
                'required' => false,
                'label' => /** @Desc("URL alias name pattern") */ 'content_type.url_alias_schema',
                'empty_data' => false,
                'disabled' => $isTranslation,
            ])
            ->add('isContainer', CheckboxType::class, [
                'required' => false,
                'label' => /** @Desc("Container") */ 'content_type.is_container',
                'disabled' => $isTranslation,
            ])
            ->add('defaultSortField', SortFieldChoiceType::class, [
                'label' => /** @Desc("Sort children by default by:") */ 'content_type.default_sort_field',
                'disabled' => $isTranslation,
            ])
            ->add('defaultSortOrder', SortOrderChoiceType::class, [
                'label' => /** @Desc("Sort children by default in order:") */ 'content_type.default_sort_order',
                'disabled' => $isTranslation,
            ])
            ->add('defaultAlwaysAvailable', CheckboxType::class, [
                'required' => false,
                'label' => /** @Desc("Make content available even with missing translations") */ 'content_type.default_always_available',
                'disabled' => $isTranslation,
            ])
            ->add('fieldDefinitionsData', CollectionType::class, [
                'entry_type' => FieldDefinitionType::class,
                'entry_options' => ['languageCode' => $options['languageCode'], 'mainLanguageCode' => $options['mainLanguageCode']],
                'label' => /** @Desc("Content Field definitions") */ 'content_type.field_definitions_data',
            ])
            ->add('fieldTypeSelection', FieldTypeChoiceType::class, [
                'mapped' => false,
                'label' => /** @Desc("Field Type selection") */ 'content_type.field_type_selection',
                'disabled' => $isTranslation,
            ])
            ->add('addFieldDefinition', SubmitType::class, [
                'label' => /** @Desc("Add field definition") */ 'content_type.add_field_definition',
                'disabled' => $isTranslation,
            ])
            ->add('removeFieldDefinition', SubmitType::class, [
                'label' => /** @Desc("Remove selected Field definitions") */ 'content_type.remove_field_definitions',
                'disabled' => !$hasFieldDefinition || $isTranslation,
            ])
            ->add('saveContentType', SubmitType::class, ['label' => /** @Desc("Apply") */ 'content_type.save'])
            ->add('removeDraft', SubmitType::class, ['label' => /** @Desc("Cancel") */ 'content_type.remove_draft', 'validation_groups' => false])
            ->add('publishContentType', SubmitType::class, [
                'label' => /** @Desc("OK") */ 'content_type.publish',
                'disabled' => !$hasFieldDefinition,
            ])
        ;
    }
}
