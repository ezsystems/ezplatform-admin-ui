<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformAdminUi\Validator\ValidationErrorsProcessor;
use EzSystems\EzPlatformContentForms\Validator\Constraints\FieldTypeValidator as BaseFieldTypeValidator;
use EzSystems\EzPlatformContentForms\Validator\ValidationErrorsProcessor as BaseValidationErrorsProcessor;

/**
 * @deprecated Since eZ Platform 3.0.2 class moved to EzPlatformContentForms Bundle. Use it instead.
 * @see \EzSystems\EzPlatformContentForms\Validator\Constraints\FieldTypeValidator.
 */
abstract class FieldTypeValidator extends BaseFieldTypeValidator
{
    protected function processValidationErrors(array $validationErrors)
    {
        $validationErrorsProcessor = $this->createValidationErrorProcessor();
        $validationErrorsProcessor->processValidationErrors($validationErrors);
    }

    private function createValidationErrorProcessor(): ValidationErrorsProcessor
    {
        return new ValidationErrorsProcessor(
            new BaseValidationErrorsProcessor(
                $this->context,
                function ($index, $target) {
                    return $this->generatePropertyPath($index, $target);
                }
            )
        );
    }
}
