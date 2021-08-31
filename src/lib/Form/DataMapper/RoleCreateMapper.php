<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\RoleCreateStruct;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData;

/**
 * Maps between RoleCreateStruct and RoleCreateData objects.
 */
class RoleCreateMapper implements DataMapperInterface
{
    /**
     * Maps given RoleCreateStruct object to a RoleCreateData object.
     *
     * @param \eZ\Publish\Core\Repository\Values\User\RoleCreateStruct|\eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function map(ValueObject $value): RoleCreateData
    {
        if (!$value instanceof RoleCreateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . RoleCreateStruct::class);
        }

        $data = new RoleCreateData();

        $data->setIdentifier($value->identifier);

        return $data;
    }

    /**
     * Maps given RoleCreateData object to a RoleCreateStruct object.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData $data
     *
     * @return \eZ\Publish\Core\Repository\Values\User\RoleCreateStruct
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function reverseMap($data): RoleCreateStruct
    {
        if (!$data instanceof RoleCreateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . RoleCreateData::class);
        }

        return new RoleCreateStruct([
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
