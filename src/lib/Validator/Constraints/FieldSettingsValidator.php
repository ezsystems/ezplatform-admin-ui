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
 * Will check if field settings for FieldDefinition are valid.
 */
class FieldSettingsValidator extends FieldTypeValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof FieldDefinitionData) {
            return;
        }

        $fieldType = $this->fieldTypeService->getFieldType($value->getFieldTypeIdentifier());
        $this->processValidationErrors($fieldType->validateFieldSettings($value->fieldSettings));
    }

    protected function generatePropertyPath($errorIndex, $errorTarget)
    {
        return 'fieldSettings' . $errorTarget;
    }
}
