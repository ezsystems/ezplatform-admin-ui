<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator as BasePasswordValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @deprecated Since eZ Platform 3.0.2 class moved to EzPlatformUser Bundle. Use it instead.
 * @see \EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator.
 */
class PasswordValidator extends ConstraintValidator
{
    /** @var \EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator */
    private $passwordValidator;

    public function __construct(BasePasswordValidator $passwordValidator)
    {
        $this->passwordValidator = $passwordValidator;
    }

    public function initialize(ExecutionContextInterface $context)
    {
        $this->passwordValidator->initialize($context);
    }

    public function validate($value, Constraint $constraint): void
    {
        $this->passwordValidator->validate($value, $constraint);
    }
}
