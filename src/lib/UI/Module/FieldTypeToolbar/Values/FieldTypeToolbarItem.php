<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\UI\Module\FieldTypeToolbar\Values;

final class FieldTypeToolbarItem
{
    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var bool */
    private $isSingular;

    public function __construct(string $identifier, string $name, bool $isSingular = false)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->isSingular = $isSingular;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSingular(): bool
    {
        return $this->isSingular;
    }
}
