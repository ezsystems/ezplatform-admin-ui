<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value\ContentType;

use EzSystems\EzPlatformRest\Value as RestValue;

final class FieldDefinitionDelete extends RestValue
{
    /** @var string[] */
    public $fieldDefinitionIdentifiers;

    /**
     * @param string[] $fieldDefinitionIdentifiers
     */
    public function __construct(array $fieldDefinitionIdentifiers = [])
    {
        $this->fieldDefinitionIdentifiers = $fieldDefinitionIdentifiers;
    }
}
