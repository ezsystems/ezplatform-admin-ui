<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\DataMapper;

use Ibexa\Contracts\AdminUi\Form\DataMapper\DataMapperInterface;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\RoleCreateStruct;
use Ibexa\AdminUi\Form\Data\Role\RoleCreateData;
use Ibexa\AdminUi\Exception\InvalidArgumentException;

/**
 * Maps between RoleCreateStruct and RoleCreateData objects.
 */
class RoleCreateMapper implements DataMapperInterface
{
    /**
     * Maps given RoleCreateStruct object to a RoleCreateData object.
     *
     * @param RoleCreateStruct|ValueObject $value
     *
     * @return RoleCreateData
     *
     * @throws InvalidArgumentException
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
     * @param RoleCreateData $data
     *
     * @return RoleCreateStruct
     *
     * @throws InvalidArgumentException
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

class_alias(RoleCreateMapper::class, 'EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleCreateMapper');
