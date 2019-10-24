<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Will check if FieldDefinition identifier is not already used within ContentType.
 */
class UniqueFieldDefinitionIdentifierValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param FieldDefinitionData $value The value that should be validated
     * @param Constraint|UniqueFieldDefinitionIdentifier $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof FieldDefinitionData) {
            return;
        }

        $contentTypeData = $value->contentTypeData;
        foreach ($contentTypeData->fieldDefinitionsData as $fieldDefData) {
            if ($fieldDefData === $value) {
                continue;
            }

            if ($value->identifier === $fieldDefData->identifier) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('identifier')
                    ->setParameter('%identifier%', $value->identifier)
                    ->addViolation();
            }
        }
    }
}
