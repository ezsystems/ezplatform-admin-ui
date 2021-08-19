<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\DataMapper;

use Ibexa\Contracts\AdminUi\Form\DataMapper\DataMapperInterface;
use eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\AdminUi\Form\Data\Content\Translation\MainTranslationUpdateData;

class MainTranslationUpdateMapper implements DataMapperInterface
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct|\eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\MainTranslationUpdateData
     */
    public function map(ValueObject $value)
    {
        if (!$value instanceof ContentMetadataUpdateStruct) {
            throw new InvalidArgumentException('value', sprintf('must be an instance of %s', ContentMetadataUpdateStruct::class));
        }

        $data = new MainTranslationUpdateData();
        $data->setLanguageCode($value->mainLanguageCode);

        return $data;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\MainTranslationUpdateData $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct
     */
    public function reverseMap($data)
    {
        if (!$data instanceof MainTranslationUpdateData) {
            throw new InvalidArgumentException('value', sprintf('must be an instance of %s', MainTranslationUpdateData::class));
        }

        return new ContentMetadataUpdateStruct([
            'mainLanguageCode' => $data->getLanguageCode(),
            'name' => $data->getContent()->getName($data->getLanguageCode()),
        ]);
    }
}

class_alias(MainTranslationUpdateMapper::class, 'EzSystems\EzPlatformAdminUi\Form\DataMapper\MainTranslationUpdateMapper');
