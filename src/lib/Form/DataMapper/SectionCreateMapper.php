<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\SectionCreateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

class SectionCreateMapper implements DataMapperInterface
{
    /**
     * @param SectionCreateStruct|ValueObject $value
     * @return SectionCreateData
     * @throws InvalidArgumentException
     */
    public function map(ValueObject $value): SectionCreateData
    {
        if (!$value instanceof SectionCreateStruct) {
            throw new InvalidArgumentException('value', 'must be instance of ' . SectionCreateStruct::class);
        }

        return new SectionCreateData($value->identifier, $value->name);
    }

    /**
     * @param SectionCreateData $data
     * @return SectionCreateStruct
     * @throws InvalidArgumentException
     */
    public function reverseMap($data): SectionCreateStruct
    {
        if (!$data instanceof SectionCreateData) {
            throw new InvalidArgumentException('data', 'must be instance of ' . SectionCreateData::class);
        }

        return new SectionCreateStruct([
            'name' => $data->getName(),
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
