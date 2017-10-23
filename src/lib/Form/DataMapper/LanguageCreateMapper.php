<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
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
     * @param LanguageCreateStruct|ValueObject $value
     *
     * @return LanguageCreateData
     *
     * @throws InvalidArgumentException
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
     * @param LanguageCreateData $data
     *
     * @return LanguageCreateStruct
     *
     * @throws InvalidArgumentException
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
