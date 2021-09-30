<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\SPI\FieldType\Value;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Generic data transformer for FieldTypes values.
 * Uses FieldType::toHash() / FieldType::fromHash().
 */
class FieldValueTransformer implements DataTransformerInterface
{
    /**
     * @var \eZ\Publish\API\Repository\FieldType
     */
    private $fieldType;

    public function __construct(FieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    /**
     * Transforms a FieldType Value into a hash using `FieldTpe::toHash()`.
     * This hash is compatible with `reverseTransform()`.
     *
     * @param mixed $value
     *
     * @return array|null the value's hash, or null if $value was not a FieldType Value
     */
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        return $this->fieldType->toHash($value);
    }

    /**
     * Transforms a hash into a FieldType Value using `FieldType::fromHash()`.
     * The FieldValue is compatible with `transform()`.
     *
     * @param mixed $value
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return $this->fieldType->getEmptyValue();
        }

        return $this->fieldType->fromHash($value);
    }
}

class_alias(FieldValueTransformer::class, 'EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\FieldValueTransformer');
