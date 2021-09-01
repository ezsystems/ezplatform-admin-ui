<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\AdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use Ibexa\Contracts\AdminUi\Form\DataMapper\DataMapperInterface;

/**
 * Maps between ContentMetadataUpdateStruct and ContentMetadataUpdateData objects.
 */
class ContentMainLocationUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given ContentMetadataUpdateStruct object to a ContentMainLocationUpdateData object.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct|\eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function map(ValueObject $value): ContentMainLocationUpdateData
    {
        if (!$value instanceof ContentMetadataUpdateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . ContentMetadataUpdateStruct::class);
        }

        $data = new ContentMainLocationUpdateData();

        $data->setLocation($value->mainLocationId);

        return $data;
    }

    /**
     * Maps given ContentMainLocationUpdateData object to a ContentMetadataUpdateStruct object.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function reverseMap($data): ContentMetadataUpdateStruct
    {
        if (!$data instanceof ContentMainLocationUpdateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . ContentMainLocationUpdateData::class);
        }

        return new ContentMetadataUpdateStruct([
            'mainLocationId' => $data->getLocation()->id,
        ]);
    }
}

class_alias(ContentMainLocationUpdateMapper::class, 'EzSystems\EzPlatformAdminUi\Form\DataMapper\ContentMainLocationUpdateMapper');
