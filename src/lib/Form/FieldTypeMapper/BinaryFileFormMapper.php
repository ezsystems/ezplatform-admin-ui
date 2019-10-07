<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\BinaryFile\Value;
use EzSystems\EzPlatformAdminUi\ConfigResolver\MaxUploadSize;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\BinaryFileValueTransformer;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\FieldType\FieldValueFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\BinaryFileFieldType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Range;

class BinaryFileFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /** @var FieldTypeService */
    private $fieldTypeService;

    /** @var MaxUploadSize */
    private $maxUploadSize;

    public function __construct(FieldTypeService $fieldTypeService, MaxUploadSize $maxUploadSize)
    {
        $this->fieldTypeService = $fieldTypeService;
        $this->maxUploadSize = $maxUploadSize;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('maxSize', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[FileSizeValidator][maxFileSize]',
                'label' => 'field_definition.ezbinaryfile.max_file_size',
                'translation_domain' => 'ezrepoforms_content_type',
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
            ]);
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        BinaryFileFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                        ]
                    )
                    ->addModelTransformer(new BinaryFileValueTransformer($fieldType, $data->value, Value::class))
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
}
