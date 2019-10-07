<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\ImageAsset\Value;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\ImageAssetValueTransformer;
use EzSystems\EzPlatformAdminUi\FieldType\FieldValueFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\ImageAssetFieldType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageAssetFormMapper implements FieldValueFormMapperInterface
{
    /** @var \eZ\Publish\API\Repository\FieldTypeService */
    private $fieldTypeService;

    /**
     * @param \eZ\Publish\API\Repository\FieldTypeService $fieldTypeService
     */
    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $fieldForm
     * @param \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData $data
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        ImageAssetFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                        ]
                    )
                    ->addModelTransformer(new ImageAssetValueTransformer($fieldType, $data->value, Value::class))
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * Fake method to set the translation domain for the extractor.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
