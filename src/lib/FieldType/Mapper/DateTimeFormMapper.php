<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use eZ\Publish\Core\FieldType\DateAndTime\Type;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformContentForms\Form\Type\DateTimeIntervalType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezdatetime FieldType.
 */
class DateTimeFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('useSeconds', CheckboxType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[useSeconds]',
                'label' => 'field_definition.ezdatetime.use_seconds',
                'disabled' => $isTranslation,
            ])
            ->add('defaultType', ChoiceType::class, [
                'choices' => [
                    'field_definition.ezdatetime.default_type_empty' => Type::DEFAULT_EMPTY,
                    'field_definition.ezdatetime.default_type_current' => Type::DEFAULT_CURRENT_DATE,
                    'field_definition.ezdatetime.default_type_adjusted' => Type::DEFAULT_CURRENT_DATE_ADJUSTED,
                ],
                'expanded' => true,
                'required' => true,
                'property_path' => 'fieldSettings[defaultType]',
                'label' => 'field_definition.ezdatetime.default_type',
                'disabled' => $isTranslation,
            ])
            ->add('dateInterval', DateTimeIntervalType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[dateInterval]',
                'label' => 'field_definition.ezdatetime.date_interval',
                'disabled' => $isTranslation,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezplatform_content_forms_content_type',
            ]);
    }
}
