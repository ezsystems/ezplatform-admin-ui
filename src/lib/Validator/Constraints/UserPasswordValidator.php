<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformUser\Validator\Constraints\UserPasswordValidator as BaseUserPasswordValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Will check if logged user and password are match.
 *
 * @deprecated Since eZ Platform 3.0.2 class moved to EzPlatformUser Bundle. Use it instead.
 * @see \EzSystems\EzPlatformUser\Validator\Constraints\UserPasswordValidator.
 */
class UserPasswordValidator extends ConstraintValidator
{
    /** @var \EzSystems\EzPlatformAdminUi\Validator\Constraints\UserPasswordValidator */
    private $passwordValidator;

    public function __construct(BaseUserPasswordValidator $passwordValidator)
    {
        $this->passwordValidator = $passwordValidator;
    }

    public function initialize(ExecutionContextInterface $context)
    {
        $this->passwordValidator->initialize($context);
    }

    /**
     * Checks if the passed password exists for logged user.
     *
     * @param string $password The password that should be validated
     * @param \Symfony\Component\Validator\Constraint|UserPassword $constraint The constraint for the validation
     *
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function validate($password, Constraint $constraint)
    {
        $this->passwordValidator->validate($password, $constraint);
    }
}
