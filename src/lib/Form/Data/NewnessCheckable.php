<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Data;

interface NewnessCheckable
{
    /**
     * Whether the Data object can be considered new.
     */
    public function isNew(): bool;
}

class_alias(NewnessCheckable::class, 'EzSystems\EzPlatformAdminUi\Form\Data\NewnessCheckable');
