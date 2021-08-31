<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\Media\Type;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\ConfigResolver\MaxUploadSize;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class MediaFormMapper implements FieldDefinitionFormMapperInterface
{
    /** @var \EzSystems\EzPlatformContentForms\ConfigResolver\MaxUploadSize */
    private $maxUploadSize;

    protected const ACCEPT_VIDEO = 'video/*';
    protected const ACCEPT_AUDIO = 'audio/*';

    public function __construct(FieldTypeService $fieldTypeService, MaxUploadSize $maxUploadSize)
    {
        $this->maxUploadSize = $maxUploadSize;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('maxSize', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[FileSizeValidator][maxFileSize]',
                'label' => /** @Desc("Maximum file size (MB)") */ 'field_definition.ezmedia.max_file_size',
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
                    /** @Desc("Flash") */
                    'field_definition.ezmedia.type_flash' => Type::TYPE_FLASH,
                    /** @Desc("HTML5 video") */
                    'field_definition.ezmedia.type_html5_video' => Type::TYPE_HTML5_VIDEO,
                    /** @Desc("QuickTime") */
                    'field_definition.ezmedia.type_quick_time' => Type::TYPE_QUICKTIME,
                    /** @Desc("RealPlayer") */
                    'field_definition.ezmedia.type_real_player' => Type::TYPE_REALPLAYER,
                    /** @Desc("Silverlight") */
                    'field_definition.ezmedia.type_silverlight' => Type::TYPE_SILVERLIGHT,
                    /** @Desc("Windows Media Player") */
                    'field_definition.ezmedia.type_windows_media_player' => Type::TYPE_WINDOWSMEDIA,
                    /** @Desc("HTML5 audio") */
                    'field_definition.ezmedia.type_html5_audio' => Type::TYPE_HTML5_AUDIO,
                ],
                'required' => true,
                'property_path' => 'fieldSettings[mediaType]',
                'label' => /** @Desc("Media type") */ 'field_definition.ezmedia.media_type',
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
                'translation_domain' => 'content_type',
            ]);
    }
}
