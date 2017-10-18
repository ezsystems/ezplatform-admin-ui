<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

/**
 * Maps between SectionUpdateStruct and SectionUpdateData objects.
 */
class SectionUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given SectionUpdateStruct object to a SectionUpdateData object.
     * @param SectionUpdateStruct|ValueObject $value
     * @return SectionUpdateData
     * @throws InvalidArgumentException
     */
    public function map(ValueObject $value): SectionUpdateData
    {
        if (!$value instanceof SectionUpdateStruct) {
            throw new InvalidArgumentException('value', 'must be instance of ' . SectionUpdateStruct::class);
        }

        return new SectionUpdateData($value->identifier, $value->name);
    }

    /**
     * Maps given SectionUpdateData object to a SectionUpdateStruct object.
     * @param SectionUpdateData $data
     * @return SectionUpdateStruct
     * @throws InvalidArgumentException
     */
    public function reverseMap($data): SectionUpdateStruct
    {
        if (!$data instanceof SectionUpdateData) {
            throw new InvalidArgumentException('data', 'must be instance of ' . SectionUpdateData::class);
        }

        return new SectionUpdateStruct([
            'name' => $data->getName(),
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
