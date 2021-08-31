<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;

/**
 * Maps between PolicyUpdateStruct and PolicyUpdateData objects.
 */
class PolicyUpdateMapper implements DataMapperInterface
{
    /**
     * Maps given PolicyUpdateStruct object to a PolicyUpdateData object.
     *
     * @param \eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct|\eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData
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
     * Maps given PolicyUpdateData object to a PolicyUpdateStruct object.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData $data
     *
     * @return \eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct
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
