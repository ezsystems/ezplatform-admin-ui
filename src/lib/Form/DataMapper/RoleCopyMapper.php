<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\RoleCopyStruct;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCopyData;

/**
 * Maps between RoleCopyStruct and RoleCopyData objects.
 */
class RoleCopyMapper implements DataMapperInterface
{
    /**
     * Maps given RoleCopyStruct object to a RoleCopyData object.
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function map(ValueObject $value): RoleCopyData
    {
        if (!$value instanceof RoleCopyStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . RoleCopyStruct::class);
        }

        return new RoleCopyData($value->role);
    }

    /**
     * Maps given RoleCopyData object to a RoleCopyStruct object.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCopyData $data
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function reverseMap($data): RoleCopyStruct
    {
        if (!$data instanceof RoleCopyData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . RoleCopyData::class);
        }

        return new RoleCopyStruct([
            'newIdentifier' => $data->getNewIdentifier(),
        ]);
    }
}
