<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueRoleIdentifier extends Constraint
{
    /**
     * %identifier% placeholder is passed.
     *
     * @var string
     */
    public $message = 'ez.role.identifier.unique';

    public function validatedBy()
    {
        return 'ezrepoforms.validator.unique_role_identifier';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
