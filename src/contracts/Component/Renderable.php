<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\AdminUi\Component;

interface Renderable
{
    public function render(array $parameters = []): string;
}

class_alias(Renderable::class, 'EzSystems\EzPlatformAdminUi\Component\Renderable');
