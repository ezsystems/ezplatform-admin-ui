<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\User\RoleUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

/**
 * Maps between RoleUpdateStruct and RoleUpdateData objects.
 */
class RoleUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given RoleUpdateStruct object to a RoleUpdateData object
     * @param RoleUpdateStruct|ValueObject $value
     * @return RoleUpdateData
     * @throws InvalidArgumentException
     */
    public function map(ValueObject $value): RoleUpdateData
    {
        if (!$value instanceof RoleUpdateStruct) {
            throw new InvalidArgumentException('value', 'must be instance of ' . RoleUpdateStruct::class);
        }

        $data = new RoleUpdateData();

        $data->setIdentifier($value->identifier);

        return $data;
    }

    /**
     * Maps given RoleUpdateData object to a RoleUpdateStruct object
     * @param RoleUpdateData $data
     * @return RoleUpdateStruct
     * @throws InvalidArgumentException
     */
    public function reverseMap($data): RoleUpdateStruct
    {
        if (!$data instanceof RoleUpdateData) {
            throw new InvalidArgumentException('data', 'must be instance of ' . RoleUpdateData::class);
        }

        return new RoleUpdateStruct([
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
