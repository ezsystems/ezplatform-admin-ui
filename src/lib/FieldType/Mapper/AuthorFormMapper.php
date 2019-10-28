<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use eZ\Publish\Core\FieldType\Author\Type;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\EzPlatformContentForms\Form\Type\FieldType\AuthorFieldType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezauthor FieldType.
 */
class AuthorFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /**
     * @param \Symfony\Component\Form\FormInterface $fieldDefinitionForm
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData $data
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add(
                'defaultAuthor',
                ChoiceType::class,
                [
                    'choices' => [
                        'field_definition.ezauthor.default_user_empty' => Type::DEFAULT_VALUE_EMPTY,
                        'field_definition.ezauthor.default_user_current' => Type::DEFAULT_CURRENT_USER,
                    ],
                    'expanded' => true,
                    'required' => true,
                    'property_path' => 'fieldSettings[defaultAuthor]',
                    'label' => 'field_definition.ezauthor.default_author',
                    'translation_domain' => 'ezplatform_content_forms_content_type',
                    'disabled' => $isTranslation,
                ]
            );
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $fieldForm
     * @param \EzSystems\EzPlatformContentForms\Data\Content\FieldData $data
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $fieldSettings = $fieldDefinition->getFieldSettings();
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', AuthorFieldType::class, [
                        'default_author' => $fieldSettings['defaultAuthor'],
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName(),
                    ])
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * Fake method to set the translation domain for the extractor.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezplatform_content_forms_content_type',
            ]);
    }
}
