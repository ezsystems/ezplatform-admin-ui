<?php

namespace EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\SectionCreateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzPlatformAdminUi\Form\Data\Section\SectionCreateData;

class SectionCreateMapper implements DataMapperInterface
{
    /**
     * @param SectionCreateStruct|ValueObject $value
     *
     * @return SectionCreateData
     */
    public function map(ValueObject $value): SectionCreateData
    {
        return new SectionCreateData($value->identifier, $value->name);
    }

    /**
     * @param SectionCreateData $data
     *
     * @return SectionCreateStruct
     */
    public function reverseMap($data): SectionCreateStruct
    {
        return new SectionCreateStruct([
            'name' => $data->getName(),
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
