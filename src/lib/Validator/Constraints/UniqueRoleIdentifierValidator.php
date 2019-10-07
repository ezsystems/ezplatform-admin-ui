<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\RoleData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueRoleIdentifierValidator extends ConstraintValidator
{
    /**
     * @var RoleService
     */
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof RoleData || $value->identifier === null) {
            return;
        }

        try {
            $role = $this->roleService->loadRoleByIdentifier($value->identifier);
            // It is of course OK to edit a draft of an existing Role :-)
            if ($role->id === $value->roleDraft->id) {
                return;
            }

            $this->context->buildViolation($constraint->message)
                ->atPath('identifier')
                ->setParameter('%identifier%', $value->identifier)
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
