<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Validator\Constraints\FieldTypeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Will check if validator configuration for FieldDefinition is valid.
 */
class ValidatorConfigurationValidator extends FieldTypeValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof FieldDefinitionData) {
            return;
        }

        $fieldType = $this->fieldTypeService->getFieldType($value->getFieldTypeIdentifier());
        $this->processValidationErrors($fieldType->validateValidatorConfiguration($value->validatorConfiguration));
    }

    protected function generatePropertyPath($errorIndex, $errorTarget)
    {
        return 'defaultValue';
    }
}
