<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Util;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;

class ContentTypeUtil
{
    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param string $fieldTypeIdentifier
     *
     * @return bool
     */
    public function hasFieldType(ContentType $contentType, string $fieldTypeIdentifier): bool
    {
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($fieldDefinition->fieldTypeIdentifier === $fieldTypeIdentifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param string $fieldTypeIdentifier
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition[]
     */
    public function findFieldDefinitions(ContentType $contentType, string $fieldTypeIdentifier): array
    {
        $fieldTypes = [];
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($fieldDefinition->fieldTypeIdentifier === $fieldTypeIdentifier) {
                $fieldTypes[] = $fieldDefinition;
            }
        }

        return $fieldTypes;
    }
}
