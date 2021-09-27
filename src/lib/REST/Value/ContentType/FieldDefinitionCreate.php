<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value\ContentType;

use EzSystems\EzPlatformRest\Value as RestValue;

final class FieldDefinitionCreate extends RestValue
{
    /** @var string|null */
    public $fieldTypeIdentifier;

    /** @var string|null */
    public $fieldGroupIdentifier;

    /** @var int|null */
    public $position;

    public function __construct(?string $fieldTypeIdentifier, ?string $fieldGroupIdentifier, ?int $position = null)
    {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->fieldGroupIdentifier = $fieldGroupIdentifier;
        $this->position = $position;
    }
}
