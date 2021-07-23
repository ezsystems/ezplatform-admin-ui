<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

final class PrototypeFieldDefinitionData extends FieldDefinitionData
{
    /** @var string */
    private $fieldTypeIdentifier;

    public function __construct(string $fieldTypeIdentifier)
    {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->contentTypeData = new PrototypeContentTypeData();
    }

    public function getFieldTypeIdentifier()
    {
        return $this->fieldTypeIdentifier;
    }
}
