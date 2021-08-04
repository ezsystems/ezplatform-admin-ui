<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\DataMapper;

use Ibexa\Contracts\AdminUi\Form\DataMapper\DataMapperInterface;
use eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use Ibexa\AdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use Ibexa\AdminUi\Exception\InvalidArgumentException;

/**
 * Maps between ContentMetadataUpdateStruct and ContentMetadataUpdateData objects.
 */
class ContentMainLocationUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given ContentMetadataUpdateStruct object to a ContentMainLocationUpdateData object.
     *
     * @param ContentMetadataUpdateStruct|ValueObject $value
     *
     * @return ContentMainLocationUpdateData
     *
     * @throws InvalidArgumentException
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
     * @param ContentMainLocationUpdateData $data
     *
     * @return ContentMetadataUpdateStruct
     *
     * @throws InvalidArgumentException
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
