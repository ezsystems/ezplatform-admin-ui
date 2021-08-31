<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Validator\Constraints\FieldTypeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validator for default value from FieldDefinitionData.
 */
class FieldDefinitionDefaultValueValidator extends FieldTypeValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof FieldDefinitionData) {
            return;
        }

        $fieldValue = $this->getFieldValue($value);
        if (!$fieldValue) {
            return;
        }

        $fieldTypeIdentifier = $this->getFieldTypeIdentifier($value);
        $fieldDefinition = $this->getFieldDefinition($value);
        $fieldType = $this->fieldTypeService->getFieldType($fieldTypeIdentifier);

        $validationErrors = $fieldType->validateValue($fieldDefinition, $fieldValue);

        $this->processValidationErrors($validationErrors);
    }

    protected function getFieldValue(FieldDefinitionData $value): ?Value
    {
        return $value->defaultValue;
    }

    /**
     * Returns the field definition $value refers to.
     * FieldDefinition object is needed to validate field value against field settings.
     */
    protected function getFieldDefinition(FieldDefinitionData $value): FieldDefinition
    {
        return $value->fieldDefinition;
    }

    /**
     * Returns the fieldTypeIdentifier for the field value to validate.
     */
    protected function getFieldTypeIdentifier(FieldDefinitionData $value): string
    {
        return $value->getFieldTypeIdentifier();
    }

    protected function generatePropertyPath($errorIndex, $errorTarget): string
    {
        return 'defaultValue';
    }
}
