<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\FieldValueTransformer;
use EzSystems\EzPlatformAdminUi\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Generic FieldValueFormMapper that uses a named FormType to generate a FieldValue Form.
 *
 * The FieldValueTransformer is used as a model transformer.
 *
 * Example in YAML service definition:
 * ```
 * ezrepoforms.field_type.form_mapper.ezuser:
 *   class: "%ezrepoforms.field_type.form_mapper.form_type_based.class%"
 *   parent: ezrepoforms.field_type.form_mapper.form_type_based
 *   tags:
 *     - { name: "ez.formMapper.fieldValue", fieldType: "ezuser" }
 *   calls:
 *     - [setFormType, ["ezuser"]]
 * ```
 */
final class FormTypeBasedFieldValueFormMapper implements FieldValueFormMapperInterface
{
    /**
     * The FormType used by the mapper. Example: '\Symfony\Component\Form\Extension\Core\Type\TextType'.
     *
     * @var string
     */
    private $formType;

    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function setFormType($formType)
    {
        $this->formType = $formType;
    }

    /**
     * Maps Field form to current FieldType based on the configured form type (self::$formType).
     *
     * @param FormInterface $fieldForm form for the current Field
     * @param FieldData $data underlying data for current Field form
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        $this->formType,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                        ]
                    )
                    ->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier)))
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
}
