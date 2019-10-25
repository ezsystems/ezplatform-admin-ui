<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\Media\Type;
use EzSystems\EzPlatformContentForms\ConfigResolver\MaxUploadSize;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class MediaFormMapper implements FieldDefinitionFormMapperInterface
{
    /** @var FieldTypeService */
    private $fieldTypeService;

    /** @var MaxUploadSize */
    private $maxUploadSize;

    protected const ACCEPT_VIDEO = 'video/*';
    protected const ACCEPT_AUDIO = 'audio/*';

    public function __construct(FieldTypeService $fieldTypeService, MaxUploadSize $maxUploadSize)
    {
        $this->fieldTypeService = $fieldTypeService;
        $this->maxUploadSize = $maxUploadSize;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('maxSize', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[FileSizeValidator][maxFileSize]',
                'label' => 'field_definition.ezmedia.max_file_size',
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => $this->maxUploadSize->get(MaxUploadSize::MEGABYTES),
                    ]),
                ],
                'attr' => [
                    'min' => 0,
                    'max' => $this->maxUploadSize->get(MaxUploadSize::MEGABYTES),
                ],
                'disabled' => $isTranslation,
            ])
            ->add('mediaType', ChoiceType::class, [
                'choices' => [
                    'field_definition.ezmedia.type_html5_video' => Type::TYPE_HTML5_VIDEO,
                    'field_definition.ezmedia.type_flash' => Type::TYPE_FLASH,
                    'field_definition.ezmedia.type_quick_time' => Type::TYPE_QUICKTIME,
                    'field_definition.ezmedia.type_real_player' => Type::TYPE_REALPLAYER,
                    'field_definition.ezmedia.type_silverlight' => Type::TYPE_SILVERLIGHT,
                    'field_definition.ezmedia.type_windows_media_player' => Type::TYPE_WINDOWSMEDIA,
                    'field_definition.ezmedia.type_html5_audio' => Type::TYPE_HTML5_AUDIO,
                ],
                'required' => true,
                'property_path' => 'fieldSettings[mediaType]',
                'label' => 'field_definition.ezmedia.media_type',
                'disabled' => $isTranslation,
            ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezplatform_content_forms_content_type',
            ]);
    }
}
