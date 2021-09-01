<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\FieldType\Mapper;

use EzSystems\EzPlatformContentForms\Form\Type\FieldType\ISBNFieldType;
use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ISBNFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $fieldDefinition): void
    {
        $isTranslation = $fieldDefinition->contentTypeData->languageCode !== $fieldDefinition->contentTypeData->mainLanguageCode;
        $defaultValueForm = $fieldDefinitionForm
            ->getConfig()
            ->getFormFactory()
            ->createBuilder()
            ->create('defaultValue', ISBNFieldType::class, [
                'required' => false,
                'label' => /** @Desc("Default value") */ 'field_definition.ezisbn.default_value',
                'disabled' => $isTranslation,
            ])
            ->setAutoInitialize(false)
            ->getForm();

        $fieldDefinitionForm
            ->add(
                'isISBN13', CheckboxType::class, [
                    'required' => false,
                    'property_path' => 'fieldSettings[isISBN13]',
                    'label' => /** @Desc("ISBN-13 format") */ 'field_definition.ezisbn.is_isbn13',
                    'disabled' => $isTranslation,
                ]
            )
            ->add($defaultValueForm);
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

class_alias(ISBNFormMapper::class, 'EzSystems\EzPlatformAdminUi\FieldType\Mapper\ISBNFormMapper');
