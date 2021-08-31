<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

/**
 * Trait for repository data objects, provides a test for if they are newly created, based on identifier.
 */
trait NewnessChecker
{
    /**
     * Whether the Data object is considered new, based on the identifier
     * If it isn't new, one can e.g. use the identifier from the underlying value object.
     */
    public function isNew(): bool
    {
        return strpos($this->getIdentifierValue(), '__new__') === 0;
    }

    /**
     * Returns the value of the property which can be considered as the value object identifier.
     */
    abstract protected function getIdentifierValue(): string;
}
