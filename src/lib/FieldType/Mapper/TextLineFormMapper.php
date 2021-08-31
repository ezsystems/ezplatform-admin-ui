<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Form\Type\FieldType\TextLineFieldType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezstring FieldType.
 */
class TextLineFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('minLength', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[StringLengthValidator][minStringLength]',
                'label' => /** @Desc("Minimum length") */ 'field_definition.ezstring.min_length',
                'attr' => ['min' => 0],
                'disabled' => $isTranslation,
            ])
            ->add('maxLength', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[StringLengthValidator][maxStringLength]',
                'label' => /** @Desc("Maximum length") */ 'field_definition.ezstring.max_length',
                'attr' => ['min' => 0],
                'disabled' => $isTranslation,
            ])
            ->add(
                $fieldDefinitionForm
                    ->getConfig()->getFormFactory()->createBuilder()
                    ->create('defaultValue', TextLineFieldType::class, [
                        'required' => false,
                        'label' => /** @Desc("Default value") */ 'field_definition.ezstring.default_value',
                        'disabled' => $isTranslation,
                    ])
                    ->setAutoInitialize(false)
                    ->getForm()
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
