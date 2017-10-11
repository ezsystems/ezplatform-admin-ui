<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\RoleCreateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData;

class RoleCreateMapper implements DataMapperInterface
{
    /**
     * @param RoleCreateStruct $value
     *
     * @return RoleCreateData
     */
    public function map(ValueObject $value): RoleCreateData
    {
        $data = new RoleCreateData();

        $data->setIdentifier($value->identifier);

        return $data;
    }

    /**
     * @param RoleCreateData $data
     *
     * @return RoleCreateStruct
     */
    public function reverseMap($data): RoleCreateStruct
    {
        return new RoleCreateStruct([
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
