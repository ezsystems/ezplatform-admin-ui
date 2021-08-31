<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Form\Type\FieldType\IntegerFieldType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezinteger FieldType.
 */
class IntegerFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $fieldDefinition): void
    {
        $isTranslation = $fieldDefinition->contentTypeData->languageCode !== $fieldDefinition->contentTypeData->mainLanguageCode;
        $defaultValueForm = $fieldDefinitionForm
            ->getConfig()
            ->getFormFactory()
            ->createBuilder()
            ->create('defaultValue', IntegerFieldType::class, [
                'required' => false,
                'label' => /** @Desc("Default value") */ 'field_definition.ezinteger.default_value',
            ])
            ->setAutoInitialize(false)
            ->getForm();

        $fieldDefinitionForm
            ->add(
                'minValue', IntegerType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[IntegerValueValidator][minIntegerValue]',
                    'label' => /** @Desc("Minimum value") */ 'field_definition.ezinteger.min_value',
                    'disabled' => $isTranslation,
                ]
            )
            ->add(
                'maxValue', IntegerType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[IntegerValueValidator][maxIntegerValue]',
                    'label' => /** @Desc("Maximum value") */ 'field_definition.ezinteger.max_value',
                    'disabled' => $isTranslation,
                ]
            )
            ->add($defaultValueForm);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }
}
