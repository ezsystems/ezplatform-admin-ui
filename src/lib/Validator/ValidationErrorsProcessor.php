<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator;

use EzSystems\EzPlatformContentForms\Validator\ValidationErrorsProcessor as BaseValidationErrorProcessor;

/**
 * @internal
 *
 * @deprecated Since eZ Platform 3.0.2 class moved to EzPlatformContentForms Bundle.
 * @see \EzSystems\EzPlatformContentForms\Validator\ValidationErrorsProcessor.
 */
final class ValidationErrorsProcessor
{
    /** @var \EzSystems\EzPlatformContentForms\Validator\ValidationErrorsProcessor */
    private $validationErrorsProcessor;

    public function __construct(BaseValidationErrorProcessor $validationErrorsProcessor)
    {
        $this->validationErrorsProcessor = $validationErrorsProcessor;
    }

    /**
     * Builds constraint violations based on given SPI validation errors.
     *
     * @param \eZ\Publish\SPI\FieldType\ValidationError[] $validationErrors
     */
    public function processValidationErrors(array $validationErrors): void
    {
        $this->validationErrorsProcessor->processValidationErrors($validationErrors);
    }
}
