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

class LanguageCreateMapper implements DataMapperInterface
{
    /**
     * @param LanguageCreateStruct|ValueObject $value
     * @return LanguageCreateData
     * @throws InvalidArgumentException
     */
    public function map(ValueObject $value): LanguageCreateData
    {
        if(!$value instanceof LanguageCreateStruct){
            throw new InvalidArgumentException('value', 'must be instance of ' . LanguageCreateStruct::class);
        }

        $data = new LanguageCreateData();

        $data->setName($value->name);
        $data->setLanguageCode($value->languageCode);
        $data->setEnabled($value->enabled);

        return $data;
    }

    /**
     * @param LanguageCreateData $data
     *
     * @return LanguageCreateStruct
     */
    public function reverseMap($data): LanguageCreateStruct
    {
        return new LanguageCreateStruct([
            'languageCode' => $data->getLanguageCode(),
            'name' => $data->getName(),
            'enabled' => $data->isEnabled(),
        ]);
    }
}
