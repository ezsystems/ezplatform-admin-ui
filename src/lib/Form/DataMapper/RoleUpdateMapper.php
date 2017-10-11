<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\User\RoleUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData;

class RoleUpdateMapper implements DataMapperInterface
{
    /**
     * @param RoleUpdateStruct $value
     *
     * @return RoleUpdateData
     */
    public function map(ValueObject $value): RoleUpdateData
    {
        $data = new RoleUpdateData();

        $data->setIdentifier($value->identifier);

        return $data;
    }

    /**
     * @param RoleUpdateData $data
     *
     * @return RoleUpdateStruct
     */
    public function reverseMap($data): RoleUpdateStruct
    {
        return new RoleUpdateStruct([
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
