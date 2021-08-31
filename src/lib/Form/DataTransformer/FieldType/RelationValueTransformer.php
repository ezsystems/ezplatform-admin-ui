<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\Core\FieldType\Relation\Value;
use Symfony\Component\Form\DataTransformerInterface;

class RelationValueTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        if ($value->destinationContentId === null) {
            return null;
        }

        return $value->destinationContentId;
    }

    public function reverseTransform($value)
    {
        if ($value === null || !is_numeric($value)) {
            return null;
        }

        return new Value($value);
    }
}
