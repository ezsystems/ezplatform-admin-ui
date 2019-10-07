<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator;

use eZ\Publish\API\Repository\Values\Translation\Plural;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @internal
 */
final class ValidationErrorsProcessor
{
    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $context;

    /** @var callable|null */
    private $propertyPathGenerator;

    /**
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     * @param callable|null $propertyPathGenerator
     */
    public function __construct(ExecutionContextInterface $context, callable $propertyPathGenerator = null)
    {
        $this->context = $context;
        $this->propertyPathGenerator = $propertyPathGenerator;
    }

    /**
     * Builds constraint violations based on given SPI validation errors.
     *
     * @param \eZ\Publish\SPI\FieldType\ValidationError[] $validationErrors
     */
    public function processValidationErrors(array $validationErrors): void
    {
        if (empty($validationErrors)) {
            return;
        }

        $propertyPathGenerator = $this->propertyPathGenerator;
        foreach ($validationErrors as $i => $error) {
            $message = $error->getTranslatableMessage();
            /** @var \Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface $violationBuilder */
            $violationBuilder = $this->context->buildViolation($message instanceof Plural ? $message->plural : $message->message);
            $violationBuilder->setParameters($message->values);

            if ($propertyPathGenerator !== null) {
                $propertyPath = $propertyPathGenerator($i, $error->getTarget());
                if ($propertyPath) {
                    $violationBuilder->atPath($propertyPath);
                }
            }

            $violationBuilder->addViolation();
        }
    }
}
