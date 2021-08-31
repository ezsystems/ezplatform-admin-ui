<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\SectionCreateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData;

/**
 * Maps between SectionCreateStruct and SectionCreateData objects.
 */
class SectionCreateMapper implements DataMapperInterface
{
    /**
     * Maps given SectionCreateStruct object to a SectionCreateData object.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\SectionCreateStruct|\eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function map(ValueObject $value): SectionCreateData
    {
        if (!$value instanceof SectionCreateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . SectionCreateStruct::class);
        }

        return new SectionCreateData($value->identifier, $value->name);
    }

    /**
     * Maps given SectionCreateData object to a SectionCreateStruct object.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\SectionCreateStruct
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function reverseMap($data): SectionCreateStruct
    {
        if (!$data instanceof SectionCreateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . SectionCreateData::class);
        }

        return new SectionCreateStruct([
            'name' => $data->getName(),
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
