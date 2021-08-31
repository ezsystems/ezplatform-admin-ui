<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component\Renderer;

interface RendererInterface
{
    public function renderGroup(string $groupName, array $parameters = []): array;

    public function renderSingle(string $name, $groupName, array $parameters = []): string;
}
