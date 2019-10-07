<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role;

use eZ\Publish\API\Repository\Values\User\RoleUpdateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessChecker;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessCheckable;

/**
 * Base data class for ContentType update form, with FieldDefinitions data and ContentTypeDraft.
 *
 * @property \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft
 */
class RoleData extends RoleUpdateStruct implements NewnessCheckable
{
    /**
     * Trait which provides isNew(), and mandates getIdentifier().
     */
    use NewnessChecker;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\RoleDraft
     */
    protected $roleDraft;

    protected function getIdentifierValue()
    {
        return $this->roleDraft->identifier;
    }
}
