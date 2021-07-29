<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value\ContentType;

use EzSystems\EzPlatformRest\Value as RestValue;

final class FieldDefinitionCreate extends RestValue
{
    /** @var string */
    public $fieldTypeIdentifier;

    /** @var int|null */
    public $position;

    public function __construct(?string $fieldTypeIdentifier, ?int $position = null)
    {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->position = $position;
    }
}
