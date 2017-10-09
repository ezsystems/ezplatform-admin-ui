<?php

namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;

class PolicyUpdateMapper implements DataMapperInterface
{
    /**
     * @param PolicyUpdateStruct $value
     *
     * @return PolicyUpdateData
     */
    public function map(ValueObject $value): PolicyUpdateData
    {
        $data = new PolicyUpdateData();

        $data->setModule($value->module);
        $data->setFunction($value->function);
        $data->setLimitations($value->getLimitations());

        return $data;
    }

    /**
     * @param PolicyUpdateData $data
     *
     * @return PolicyUpdateStruct
     */
    public function reverseMap($data): PolicyUpdateStruct
    {
        $policyUpdateStruct = new PolicyUpdateStruct();

        foreach ($data->getLimitations() as $limitation) {
            if (!empty($limitation->limitationValues)) {
                $policyUpdateStruct->addLimitation($limitation);
            }
        }

        return $policyUpdateStruct;
    }
}