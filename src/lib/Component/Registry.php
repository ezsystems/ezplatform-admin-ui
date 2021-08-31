<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component;

class Registry
{
    /** @var Renderable[] */
    protected $components;

    /**
     * @param Renderable[] $components
     */
    public function __construct(array $components = [])
    {
        $this->components = $components;
    }

    /**
     * @param string $group
     * @param string $serviceId
     * @param Renderable $component
     */
    public function addComponent(string $group, string $serviceId, Renderable $component): void
    {
        $this->components[$group][$serviceId] = $component;
    }

    /**
     * @param string $group
     *
     * @return Renderable[]
     */
    public function getComponents(string $group): array
    {
        return $this->components[$group] ?? [];
    }

    /**
     * @param string $group
     * @param array $components
     */
    public function setComponents(string $group, array $components)
    {
        $this->components[$group] = $components;
    }
}
