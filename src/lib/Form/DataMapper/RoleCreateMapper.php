<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\RoleCreateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

class RoleCreateMapper implements DataMapperInterface
{
    /**
     * @param ValueObject|RoleCreateStruct $value
     * @return RoleCreateData
     * @throws InvalidArgumentException
     */
    public function map(ValueObject $value): RoleCreateData
    {
        if (!$value instanceof RoleCreateStruct) {
            throw new InvalidArgumentException('value', 'must be instance of ' . RoleCreateStruct::class);
        }

        $data = new RoleCreateData();

        $data->setIdentifier($value->identifier);

        return $data;
    }

    /**
     * @param RoleCreateData $data
     * @return RoleCreateStruct
     * @throws InvalidArgumentException
     */
    public function reverseMap($data): RoleCreateStruct
    {
        if (!$data instanceof RoleCreateData) {
            throw new InvalidArgumentException('data', 'must be instance of ' . RoleCreateData::class);
        }

        return new RoleCreateStruct([
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
