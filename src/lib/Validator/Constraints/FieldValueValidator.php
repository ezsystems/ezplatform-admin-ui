<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\FieldType\ValidationError;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Util\PropertyPath;

/**
 * Base class for field value validators.
 */
class FieldValueValidator extends FieldTypeValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ValueObject) {
            return;
        }

        $fieldValue = $this->getFieldValue($value);
        if (!$fieldValue) {
            return;
        }

        $fieldTypeIdentifier = $this->getFieldTypeIdentifier($value);
        $fieldDefinition = $this->getFieldDefinition($value);
        $fieldType = $this->fieldTypeService->getFieldType($fieldTypeIdentifier);

        $validationErrors = [];
        if (!($value instanceof FieldDefinitionData) && $fieldType->isEmptyValue($fieldValue)) {
            if ($fieldDefinition->isRequired) {
                $validationErrors = [
                    new ValidationError(
                        "Value for required field definition '%identifier%' with language '%languageCode%' is empty",
                        null,
                        ['%identifier%' => $fieldDefinition->identifier, '%languageCode%' => $value->field->languageCode],
                        'empty'
                    ),
                ];
            }
        } else {
            $validationErrors = $fieldType->validateValue($fieldDefinition, $fieldValue);
        }

        $this->processValidationErrors($validationErrors);
    }

    /**
     * Returns the field value to validate.
     *
     * @param FieldData|ValueObject $value fieldData ValueObject holding the field value to validate
     *
     * @throws \InvalidArgumentException if field value cannot be retrieved
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    protected function getFieldValue(ValueObject $value)
    {
        return $value->value;
    }

    /**
     * Returns the field definition $value refers to.
     * FieldDefinition object is needed to validate field value against field settings.
     *
     * @param FieldData|ValueObject $value ValueObject holding the field value to validate, e.g. FieldDefinitionData.
     *
     * @throws \InvalidArgumentException if field definition cannot be retrieved
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected function getFieldDefinition(ValueObject $value)
    {
        return $value->fieldDefinition;
    }

    /**
     * Returns the fieldTypeIdentifier for the field value to validate.
     *
     * @param FieldData|ValueObject $value fieldData ValueObject holding the field value to validate
     *
     * @return string
     */
    protected function getFieldTypeIdentifier(ValueObject $value)
    {
        return $value->fieldDefinition->fieldTypeIdentifier;
    }

    protected function generatePropertyPath($errorIndex, $errorTarget)
    {
        return PropertyPath::append('value', $errorTarget);
    }
}
