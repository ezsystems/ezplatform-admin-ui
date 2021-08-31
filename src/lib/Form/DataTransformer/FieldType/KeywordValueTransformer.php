<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\Core\FieldType\Keyword\Value;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer for Keyword\Value.
 */
class KeywordValueTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        return implode(', ', $value->values);
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        return new Value($value);
    }
}
