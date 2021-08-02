<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldDefinition;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\Field\ThumbnailStrategy;
use EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcherInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeUpdateV2Data;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionDataV2;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\TranslatablePropertyTransformer;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\FieldGroupChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldDefinitionV2Type extends AbstractType
{
    /** @var FieldTypeDefinitionFormMapperDispatcherInterface */
    private $fieldTypeMapperDispatcher;

    /** @var FieldTypeService */
    private $fieldTypeService;

    /** @var ThumbnailStrategy */
    private $thumbnailStrategy;

    public function __construct(
        FieldTypeDefinitionFormMapperDispatcherInterface $fieldTypeMapperDispatcher,
        FieldTypeService $fieldTypeService
//        ThumbnailStrategy $thumbnailStrategy
    ) {
        $this->fieldTypeMapperDispatcher = $fieldTypeMapperDispatcher;
        $this->fieldTypeService = $fieldTypeService;
//        $this->thumbnailStrategy = $thumbnailStrategy;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $translatablePropertyTransformer = new TranslatablePropertyTransformer($options['language_code']);
        $isTranslation = $options['language_code'] !== $options['main_language_code'];

        $builder
            ->add(
                $builder->create('name',
                    TextType::class,
                    [
                        'property_path' => 'names',
                        'label' => /** @Desc("Name") */ 'field_definition.name',
                    ])
                    ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add(
                'identifier',
                TextType::class,
                [
                    'label' => /** @Desc("Identifier") */ 'field_definition.identifier',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                $builder->create('description', TextType::class, [
                    'property_path' => 'descriptions',
                    'required' => false,
                    'label' => /** @Desc("Description") */ 'field_definition.description',
                ])
                ->addModelTransformer($translatablePropertyTransformer)
            )
            ->add('isRequired', CheckboxType::class, [
                'required' => false,
                'label' => /** @Desc("Required") */ 'field_definition.is_required',
                'disabled' => $isTranslation,
            ])
            ->add('isTranslatable', CheckboxType::class, [
                'required' => false,
                'label' => /** @Desc("Translatable") */ 'field_definition.is_translatable',
                'disabled' => $isTranslation,
            ])
            ->add(
                'fieldGroup', FieldGroupChoiceType::class, [
                    'required' => false,
                    'label' => /** @Desc("Category") */ 'field_definition.field_group',
                    'disabled' => $isTranslation,
                ]
            )
            ->add('position', IntegerType::class, [
                'label' => /** @Desc("Position") */ 'field_definition.position',
                'disabled' => $isTranslation,
            ]);

        // Hook on form generation for specific FieldType needs
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            /** @var \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionDataV2 $data */
            $data = $event->getData();
            $form = $event->getForm();

            $fieldTypeIdentifier = $data !== null ? $data->fgie() : $options['field_type_identifier'];
            if ($fieldTypeIdentifier === null) {
                return;
            }

//            $fieldType = $this->fieldTypeService->getFieldType($fieldTypeIdentifier);
//            $isTranslation = $data !== null ? $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode : false;
//            // isSearchable field should be present only if the FieldType allows it.
//            $form->add('isSearchable', CheckboxType::class, [
//                'required' => false,
//                'disabled' => !$fieldType->isSearchable() || $isTranslation,
//                'label' => /** @Desc("Searchable") */ 'field_definition.is_searchable',
//            ]);
//
//            $form->add('isThumbnail', CheckboxType::class, [
//                'required' => false,
//                'label' => 'field_definition.is_thumbnail',
//                'disabled' => $isTranslation || !$this->thumbnailStrategy->hasStrategy($fieldTypeIdentifier),
//            ]);
//
//            if ($data === null) {
//                $data = $this->prototypeFieldDefinitionDataFactory->createForFieldType(
//                    $options['field_type_identifier']
//                );
//            }
//
//            // Let fieldType mappers do their jobs to complete the form.
//            $this->fieldTypeMapperDispatcher->map($form, $data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FieldDefinitionDataV2::class,
            'translation_domain' => 'content_type',
            'main_language_code' => null,
        ]);

        $resolver->setAllowedTypes('main_language_code', ['null', 'string']);

        $resolver->setRequired(['language_code']);
        $resolver->setAllowedTypes('language_code', ['null', 'string']);
    }
}
