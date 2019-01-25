<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\MainTranslationUpdateData;

/**
 * Maps between ContentMetadataUpdateStruct and ContentMetadataUpdateData objects.
 */
class MainTranslationUpdateMapper implements DataMapperInterface
{
    /**
     * @param ContentMetadataUpdateStruct|ValueObject $value
     *
     * @return ContentMainLocationUpdateData
     */
    public function map(ValueObject $value)
    {
        if (!$value instanceof ContentMetadataUpdateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . ContentMetadataUpdateStruct::class);
        }

        $data = new MainTranslationUpdateData();
        $data->setLanguageCode($value->mainLanguageCode);

        return $data;
    }

    /**
     * @param MainTranslationUpdateData $data
     *
     * @return ContentMetadataUpdateStruct
     */
    public function reverseMap($data)
    {
        if (!$data instanceof MainTranslationUpdateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . MainTranslationUpdateData::class);
        }

        return new ContentMetadataUpdateStruct([
            'mainLanguageCode' => $data->getLanguageCode(),
        ]);
    }
}
