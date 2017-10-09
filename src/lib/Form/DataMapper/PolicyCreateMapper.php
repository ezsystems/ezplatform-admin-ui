<?php

namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\PolicyCreateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData;

class PolicyCreateMapper implements DataMapperInterface
{
    /**
     * @param PolicyCreateStruct $value
     *
     * @return PolicyCreateData
     */
    public function map(ValueObject $value): PolicyCreateData
    {
        $data = new PolicyCreateData();

        $data->setModule($value->module);
        $data->setFunction($value->function);

        return $data;
    }

    /**
     * @param PolicyCreateData $data
     *
     * @return PolicyCreateStruct
     */
    public function reverseMap($data): PolicyCreateStruct
    {
        return new PolicyCreateStruct([
            'module' => $data->getModule(),
            'function' => $data->getFunction(),
        ]);
    }
}