<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Data transformer for ezbinaryfile field type.
 *
 * {@inheritdoc}
 */
class BinaryFileValueTransformer extends AbstractBinaryBaseTransformer implements DataTransformerInterface
{
    /**
     * @param \eZ\Publish\Core\FieldType\BinaryFile\Value $value
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
     * @return \eZ\Publish\Core\FieldType\BinaryFile\Value
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value)
    {
        /** @var \eZ\Publish\Core\FieldType\BinaryFile\Value $valueObject */
        $valueObject = $this->getReverseTransformedValue($value);

        if ($this->fieldType->isEmptyValue($valueObject)) {
            return $valueObject;
        }

        return $valueObject;
    }
}
