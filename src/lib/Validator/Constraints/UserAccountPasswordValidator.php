<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\User\UserAccountFieldData;
use EzSystems\EzPlatformAdminUi\Validator\ValidationErrorsProcessor;
use Symfony\Component\Validator\Constraint;

class UserAccountPasswordValidator extends PasswordValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!($value instanceof UserAccountFieldData)) {
            return;
        }

        parent::validate($value->password, $constraint);
    }

    /**
     * {@inheritdoc}
     */
    protected function createValidationErrorsProcessor(): ValidationErrorsProcessor
    {
        return new ValidationErrorsProcessor($this->context, function () {
            return 'password';
        });
    }
}
