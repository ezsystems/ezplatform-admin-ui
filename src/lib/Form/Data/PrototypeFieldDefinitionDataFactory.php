<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\Core\FieldType\FieldTypeRegistry;

final class PrototypeFieldDefinitionDataFactory
{
    /** @var \eZ\Publish\Core\FieldType\FieldTypeRegistry */
    private $fieldTypeRegistry;

    public function __construct(FieldTypeRegistry $fieldTypeRegistry)
    {
        $this->fieldTypeRegistry = $fieldTypeRegistry;
    }

    public function createForFieldType(string $fieldTypeIdentifier): PrototypeFieldDefinitionData
    {
        $fieldType = $this->fieldTypeRegistry->getFieldType($fieldTypeIdentifier);

        $data = new PrototypeFieldDefinitionData($fieldTypeIdentifier);
        $data->defaultValue = $fieldType->getEmptyValue();

        $fieldType->applyDefaultSettings($data->fieldSettings);
        $fieldType->applyDefaultValidatorConfiguration($data->validatorConfiguration);

        return $data;
    }
}
