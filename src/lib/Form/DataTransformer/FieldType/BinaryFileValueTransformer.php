<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\Core\FieldType\BinaryFile\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Data transformer for ezbinaryfile field type.
 *
 * {@inheritdoc}
 */
class BinaryFileValueTransformer extends AbstractBinaryBaseTransformer implements DataTransformerInterface
{
    /**
     * @param Value $value
     *
     * @return array
     */
    public function transform($value)
    {
        if (null === $value) {
            $value = $this->fieldType->getEmptyValue();
        }

        return array_merge(
            $this->getDefaultProperties(),
            ['downloadCount' => $value->downloadCount]
        );
    }

    /**
     * @param array $value
     *
     * @return Value
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($value)
    {
        /** @var Value $valueObject */
        $valueObject = $this->getReverseTransformedValue($value);

        if ($this->fieldType->isEmptyValue($valueObject)) {
            return $valueObject;
        }

        return $valueObject;
    }
}
