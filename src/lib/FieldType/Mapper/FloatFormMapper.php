<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformContentForms\Form\Type\FieldType\FloatFieldType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezfloat FieldType.
 */
class FloatFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $fieldDefinition): void
    {
        $isTranslation = $fieldDefinition->contentTypeData->languageCode !== $fieldDefinition->contentTypeData->mainLanguageCode;
        $defaultValueForm = $fieldDefinitionForm
            ->getConfig()
            ->getFormFactory()
            ->createBuilder()
            ->create('defaultValue', FloatFieldType::class, [
                'required' => false,
                'label' => 'field_definition.ezfloat.default_value',
                'disabled' => $isTranslation,
            ])
            ->setAutoInitialize(false)
            ->getForm();

        $fieldDefinitionForm
            ->add(
                'minValue', NumberType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[FloatValueValidator][minFloatValue]',
                    'label' => 'field_definition.ezfloat.min_value',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'maxValue', NumberType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[FloatValueValidator][maxFloatValue]',
                    'label' => 'field_definition.ezfloat.max_value',
                    'disabled' => $isTranslation,
                ]
            )
            ->add($defaultValueForm);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezplatform_content_forms_content_type',
            ]);
    }
}
