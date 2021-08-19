<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\DataMapper;

use Ibexa\Contracts\AdminUi\Form\DataMapper\DataMapperInterface;
use eZ\Publish\API\Repository\Values\User\RoleUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use Ibexa\AdminUi\Form\Data\Role\RoleUpdateData;
use Ibexa\AdminUi\Exception\InvalidArgumentException;

/**
 * Maps between RoleUpdateStruct and RoleUpdateData objects.
 */
class RoleUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given RoleUpdateStruct object to a RoleUpdateData object.
     *
     * @param RoleUpdateStruct|ValueObject $value
     *
     * @return RoleUpdateData
     *
     * @throws InvalidArgumentException
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
     * @param RoleUpdateData $data
     *
     * @return RoleUpdateStruct
     *
     * @throws InvalidArgumentException
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

class_alias(RoleUpdateMapper::class, 'EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleUpdateMapper');
