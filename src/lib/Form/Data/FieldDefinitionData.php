<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct;

/**
 * Base class for FieldDefinition forms, with corresponding FieldDefinition object.
 *
 * @property \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
 * @property \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData $contentTypeData
 */
class FieldDefinitionData extends FieldDefinitionUpdateStruct
{
    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected $fieldDefinition;

    /**
     * ContentTypeData holding current FieldDefinitionData.
     * Mainly used for validation.
     *
     * @var \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData
     */
    protected $contentTypeData;

    public function getFieldTypeIdentifier()
    {
        return $this->fieldDefinition->fieldTypeIdentifier;
    }
}

class_alias(
    FieldDefinitionData::class,
    \EzSystems\RepositoryForms\Data\FieldDefinitionData::class
);
