<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct;

/**
 * Base class for FieldDefinition forms, with corresponding FieldDefinition object.
 *
 * @property \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
 * @property \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeData $contentTypeData
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
     * @var \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeData
     */
    protected $contentTypeData;

    public function getFieldTypeIdentifier()
    {
        return $this->fieldDefinition->fieldTypeIdentifier;
    }
}
