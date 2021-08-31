<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType;

use eZ\Publish\Core\FieldType\Author\Author;
use eZ\Publish\Core\FieldType\Author\Value;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer for Author\Value.
 */
class AuthorValueTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (!$value instanceof Value || $value->authors->count() == 0) {
            return [[]];
        }

        $authors = [];
        foreach ($value->authors as $author) {
            $authors[] = [
                'id' => $author->id,
                'name' => $author->name,
                'email' => $author->email,
            ];
        }

        return $authors;
    }

    public function reverseTransform($value)
    {
        if ($value === null || !is_array($value)) {
            return null;
        }

        $authors = [];
        foreach ($value as $authorProperties) {
            $authors[] = new Author($authorProperties);
        }

        return new Value($authors);
    }
}
