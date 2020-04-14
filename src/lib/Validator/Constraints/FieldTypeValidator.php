<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformAdminUi\Validator\ValidationErrorsProcessor;
use EzSystems\EzPlatformContentForms\Validator\Constraints\FieldTypeValidator as BaseFieldTypeValidator;
use EzSystems\EzPlatformContentForms\Validator\ValidationErrorsProcessor as BaseValidationErrorsProcessor;

/**
 * @deprecated
 * Use EzSystems\EzPlatformContentForms\Validator\Constraints\FieldTypeValidator instead.
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
