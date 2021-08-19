<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\DataMapper;

use Ibexa\Contracts\AdminUi\Form\DataMapper\DataMapperInterface;
use eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use Ibexa\AdminUi\Form\Data\Section\SectionUpdateData;
use Ibexa\AdminUi\Exception\InvalidArgumentException;
use eZ\Publish\API\Repository\Values\Content\Section;

/**
 * Maps between SectionUpdateStruct and SectionUpdateData objects.
 */
class SectionUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given SectionUpdateStruct object to a SectionUpdateData object.
     *
     * @param SectionUpdateStruct|ValueObject $value
     *
     * @return SectionUpdateData
     *
     * @throws InvalidArgumentException
     */
    public function map(ValueObject $value): SectionUpdateData
    {
        if (!$value instanceof SectionUpdateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . SectionUpdateStruct::class);
        }

        return new SectionUpdateData(new Section(['identifier' => $value->identifier, 'name' => $value->name]));
    }

    /**
     * Maps given SectionUpdateData object to a SectionUpdateStruct object.
     *
     * @param SectionUpdateData $data
     *
     * @return SectionUpdateStruct
     *
     * @throws InvalidArgumentException
     */
    public function reverseMap($data): SectionUpdateStruct
    {
        if (!$data instanceof SectionUpdateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . SectionUpdateData::class);
        }

        return new SectionUpdateStruct([
            'name' => $data->getName(),
            'identifier' => $data->getIdentifier(),
        ]);
    }
}

class_alias(SectionUpdateMapper::class, 'EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionUpdateMapper');
