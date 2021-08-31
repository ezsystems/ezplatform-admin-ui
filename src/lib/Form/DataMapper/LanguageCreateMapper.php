<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;

/**
 * Maps between LanguageCreateStruct and LanguageCreateData objects.
 */
class LanguageCreateMapper implements DataMapperInterface
{
    /**
     * Maps given LanguageCreateStruct object to a LanguageCreateData object.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct|\eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function map(ValueObject $value): LanguageCreateData
    {
        if (!$value instanceof LanguageCreateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . LanguageCreateStruct::class);
        }

        $data = new LanguageCreateData();

        $data->setName($value->name);
        $data->setLanguageCode($value->languageCode);
        $data->setEnabled($value->enabled);

        return $data;
    }

    /**
     * Maps given LanguageCreateData object to a LanguageCreateStruct object.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function reverseMap($data): LanguageCreateStruct
    {
        if (!$data instanceof LanguageCreateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . LanguageCreateData::class);
        }

        return new LanguageCreateStruct([
            'languageCode' => $data->getLanguageCode(),
            'name' => $data->getName(),
            'enabled' => $data->isEnabled(),
        ]);
    }
}
