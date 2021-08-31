<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\Core\FieldType\Value;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Base transformer for binary file based field types.
 *
 * {@inheritdoc}
 */
abstract class AbstractBinaryBaseTransformer
{
    /** @var \eZ\Publish\API\Repository\FieldType */
    protected $fieldType;

    /** @var \eZ\Publish\Core\FieldType\Value */
    protected $initialValue;

    /** @var string */
    protected $valueClass;

    /**
     * @param \eZ\Publish\API\Repository\FieldType $fieldType
     * @param \eZ\Publish\Core\FieldType\Value $initialValue
     * @param string $valueClass
     */
    public function __construct(FieldType $fieldType, Value $initialValue, $valueClass)
    {
        $this->fieldType = $fieldType;
        $this->initialValue = $initialValue;
        $this->valueClass = $valueClass;
    }

    /**
     * @return array|null
     */
    public function getDefaultProperties()
    {
        return [
            'file' => null,
            'remove' => false,
        ];
    }

    /**
     * @param array $value
     *
     * @return \eZ\Publish\Core\FieldType\Value
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function getReverseTransformedValue($value)
    {
        if (!is_array($value)) {
            throw new TransformationFailedException(sprintf('Received %s instead of an array', gettype($value)));
        }

        if ($value['remove']) {
            return $this->fieldType->getEmptyValue();
        }

        /* in case file is not modified, overwrite settings only */
        if (null === $value['file']) {
            return clone $this->initialValue;
        }

        $properties = [
            'inputUri' => $value['file']->getRealPath(),
            'fileName' => $value['file']->getClientOriginalName(),
            'fileSize' => $value['file']->getSize(),
        ];

        return new $this->valueClass($properties);
    }
}
