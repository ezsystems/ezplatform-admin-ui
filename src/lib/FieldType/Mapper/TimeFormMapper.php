<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use eZ\Publish\Core\FieldType\Time\Type;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for eztime FieldType.
 */
class TimeFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add(
                'useSeconds',
                CheckboxType::class,
                [
                    'required' => false,
                    'property_path' => 'fieldSettings[useSeconds]',
                    'label' => /** @Desc("Use seconds") */ 'field_definition.eztime.use_seconds',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'defaultType',
                ChoiceType::class,
                [
                    'choices' => [
                        /** @Desc("Empty") */
                        'field_definition.eztime.default_type_empty' => Type::DEFAULT_EMPTY,
                        /** @Desc("Current time") */
                        'field_definition.eztime.default_type_current' => Type::DEFAULT_CURRENT_TIME,
                    ],
                    'expanded' => true,
                    'required' => true,
                    'property_path' => 'fieldSettings[defaultType]',
                    'label' => /** @Desc("Default value") */ 'field_definition.eztime.default_type',
                    'disabled' => $isTranslation,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }
}
