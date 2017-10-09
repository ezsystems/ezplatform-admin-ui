<?php

namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;

class LanguageCreateMapper implements DataMapperInterface
{
    /**
     * @param LanguageCreateStruct $value
     *
     * @return LanguageCreateData
     */
    public function map(ValueObject $value): LanguageCreateData
    {
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