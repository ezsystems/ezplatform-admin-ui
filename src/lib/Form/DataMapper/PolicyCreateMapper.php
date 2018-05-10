<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\User\PolicyCreateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

/**
 * Maps between PolicyCreateStruct and LanguageCreateData objects.
 */
class PolicyCreateMapper implements DataMapperInterface
{
    /**
     * Maps given PolicyCreateStruct object to a PolicyCreateData object.
     *
     * @param ValueObject|PolicyCreateStruct $value
     *
     * @return PolicyCreateData
     *
     * @throws InvalidArgumentException
     */
    public function map(ValueObject $value): PolicyCreateData
    {
        if (!$value instanceof PolicyCreateStruct) {
            throw new InvalidArgumentException('value', 'must be an instance of ' . PolicyCreateStruct::class);
        }

        $data = new PolicyCreateData();

        $data->setModule($value->module);
        $data->setFunction($value->function);
        $data->setLimitations($value->getLimitations());

        return $data;
    }

    /**
     * Maps given PolicyCreateData object to a PolicyCreateStruct object.
     *
     * @param PolicyCreateData $data
     *
     * @return PolicyCreateStruct
     *
     * @throws InvalidArgumentException
     */
    public function reverseMap($data): PolicyCreateStruct
    {
        if (!$data instanceof PolicyCreateData) {
            throw new InvalidArgumentException('data', 'must be an instance of ' . PolicyCreateData::class);
        }

        $policyCreateStruct = new PolicyCreateStruct([
            'module' => $data->getModule(),
            'function' => $data->getFunction(),
        ]);

        foreach ($data->getLimitations() as $limitation) {
            if (!empty($limitation->limitationValues)) {
                $policyCreateStruct->addLimitation($limitation);
            }
        }

        return $policyCreateStruct;
    }
}
