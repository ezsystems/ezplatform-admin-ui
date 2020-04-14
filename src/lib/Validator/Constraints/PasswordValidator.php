<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator as BasePasswordValidator;

/**
 * @deprecated
 * Use \EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator instead
 */
class PasswordValidator extends ConstraintValidator
{
    /** @var \EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator */
    private $passwordValidator;

    public function __construct(BasePasswordValidator $passwordValidator)
    {
        $this->passwordValidator = $passwordValidator;
    }

    public function validate($value, Constraint $constraint): void
    {
        $this->passwordValidator->validate($value, $constraint);
    }
}
