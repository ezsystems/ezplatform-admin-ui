<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldDefinition;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\TranslatablePropertyTransformer;
use EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;

/**
 * Form type for FieldDefinition update.
 */
class FieldDefinitionType extends AbstractType
{
    /**
     * @var \EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcherInterface
     */
    private $fieldTypeMapperDispatcher;

    /**
     * @var FieldTypeService
     */
    private $fieldTypeService;

    /**
     * @var FieldsGroupsList
     */
    private $groupsList;

    public function __construct(FieldTypeDefinitionFormMapperDispatcherInterface $fieldTypeMapperDispatcher, FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeMapperDispatcher = $fieldTypeMapperDispatcher;
        $this->fieldTypeService = $fieldTypeService;
    }

    public function setGroupsList(FieldsGroupsList $groupsList)
    {
        $this->groupsList = $groupsList;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => FieldDefinitionData::class,
                'translation_domain' => 'ezplatform_content_forms_content_type',
                'mainLanguageCode' => null,
            ])
            ->setDefined(['mainLanguageCode'])
            ->setAllowedTypes('mainLanguageCode', ['null', 'string'])
            ->setRequired(['languageCode']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fieldsGroups = [];
        if (isset($this->groupsList)) {
            $fieldsGroups = array_flip($this->groupsList->getGroups());
        }

        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['languageCode']);
        $isTranslation = $options['languageCode'] !== $options['mainLanguageCode'];

        $builder
            ->add(
                $builder->create('name',
                    TextType::class,
                    [
                        'property_path' => 'names',
                        'label' => 'field_definition.name',
                    ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add(
                'identifier',
                TextType::class,
                [
                    'label' => 'field_definition.identifier',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                $builder->create('description', TextType::class, [
                    'property_path' => 'descriptions',
                    'required' => false,
                    'label' => 'field_definition.description',
                ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('isRequired', CheckboxType::class, [
                'required' => false,
                'label' => 'field_definition.is_required',
                'disabled' => $isTranslation,
            ])
            ->add('isTranslatable', CheckboxType::class, [
                'required' => false,
                'label' => 'field_definition.is_translatable',
                'disabled' => $isTranslation,
            ])
            ->add(
                'fieldGroup', ChoiceType::class, [
                    'choices' => $fieldsGroups,
                    'required' => false,
                    'label' => 'field_definition.field_group',
                    'disabled' => $isTranslation,
                ]
            )
            ->add('position', IntegerType::class, [
                'label' => 'field_definition.position',
                'disabled' => $isTranslation,
            ])
            ->add('selected', CheckboxType::class, [
                'required' => false,
                'mapped' => false,
                'disabled' => $isTranslation,
            ]);

        // Hook on form generation for specific FieldType needs
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData $data */
            $data = $event->getData();
            $form = $event->getForm();
            $fieldTypeIdentifier = $data->getFieldTypeIdentifier();
            $fieldType = $this->fieldTypeService->getFieldType($fieldTypeIdentifier);
            $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
            // isSearchable field should be present only if the FieldType allows it.
            $form->add('isSearchable', CheckboxType::class, [
                'required' => false,
                'disabled' => !$fieldType->isSearchable() || $isTranslation,
                'label' => 'field_definition.is_searchable',
            ]);

            // Let fieldType mappers do their jobs to complete the form.
            $this->fieldTypeMapperDispatcher->map($form, $data);
        });
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_content_forms_fielddefinition_update';
    }
}
