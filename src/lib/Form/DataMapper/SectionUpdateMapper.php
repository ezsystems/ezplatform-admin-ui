<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;

/**
 * Maps between SectionUpdateStruct and SectionUpdateData objects.
 */
class SectionUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given SectionUpdateStruct object to a SectionUpdateData object.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct|\eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
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
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
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
