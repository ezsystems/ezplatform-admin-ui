<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;

class SectionUpdateMapper implements DataMapperInterface
{
    /**
     * @param SectionUpdateStruct|ValueObject $value
     *
     * @return SectionUpdateData
     */
    public function map(ValueObject $value): SectionUpdateData
    {
        return new SectionUpdateData($value->identifier, $value->name);
    }

    /**
     * @param SectionUpdateData $data
     *
     * @return SectionUpdateStruct
     */
    public function reverseMap($data): SectionUpdateStruct
    {
        return new SectionUpdateStruct([
            'name' => $data->getName(),
            'identifier' => $data->getIdentifier(),
        ]);
    }
}
