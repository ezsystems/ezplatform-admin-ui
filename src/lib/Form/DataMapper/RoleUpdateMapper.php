<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\User\RoleUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData;

/**
 * Maps between RoleUpdateStruct and RoleUpdateData objects.
 */
class RoleUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given RoleUpdateStruct object to a RoleUpdateData object.
     *
     * @param \eZ\Publish\API\Repository\Values\User\RoleUpdateStruct|\eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function map(ValueObject $value): RoleUpdateData
    {
        if (!$value instanceof RoleUpdateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . RoleUpdateStruct::class);
        }

        $data = new RoleUpdateData();

        $data->setIdentifier($value->identifier);

        return $data;
    }

    /**
     * Maps given RoleUpdateData object to a RoleUpdateStruct object.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData $data
     *
     * @return \eZ\Publish\API\Repository\Values\User\RoleUpdateStruct
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function reverseMap($data): RoleUpdateStruct
    {
        if (!$data instanceof RoleUpdateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . RoleUpdateData::class);
        }

        return new RoleUpdateStruct([
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
