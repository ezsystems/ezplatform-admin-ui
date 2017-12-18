<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component\Renderer;

interface RendererInterface
{
    public function renderGroup(string $groupName, array $parameters = []): array;

    public function renderSingle(string $name, $groupName, array $parameters = []): string;
}