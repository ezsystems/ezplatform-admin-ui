<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\Core\FieldType\RelationList\Value;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer for RelationList\Value in single select mode.
 */
class RelationListValueTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        if ($value->destinationContentIds === []) {
            return null;
        }

        return implode(',', $value->destinationContentIds);
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        $destinationContentIds = explode(',', $value);
        $destinationContentIds = array_map('trim', $destinationContentIds);

        return new Value($destinationContentIds);
    }
}
