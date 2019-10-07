<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\RoleData;

class RoleMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data
     * (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\User\RoleDraft|ValueObject $role
     * @param array $params
     *
     * @return RoleData
     */
    public function mapToFormData(ValueObject $roleDraft, array $params = [])
    {
        $roleData = new RoleData(['roleDraft' => $roleDraft]);
        if (!$roleData->isNew()) {
            $roleData->identifier = $roleDraft->identifier;
        }

        return $roleData;
    }
}
