<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component\Renderer;

use EzSystems\EzPlatformAdminUi\Component\Event\RenderGroupEvent;
use EzSystems\EzPlatformAdminUi\Component\Event\RenderSingleEvent;
use EzSystems\EzPlatformAdminUi\Component\Registry;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DefaultRenderer implements RendererInterface
{
    protected $registry;

    protected $eventDispatcher;

    public function __construct(Registry $registry, EventDispatcherInterface $eventDispatcher)
    {
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function renderGroup(string $groupName, array $parameters = []): array
    {
        $this->eventDispatcher->dispatch(new RenderGroupEvent(
            $this->registry,
            $groupName,
            $parameters
        ), RenderGroupEvent::NAME);

        $components = $this->registry->getComponents($groupName);

        $rendered = [];
        foreach ($components as $id => $component) {
            $rendered[] = $this->renderSingle($id, $groupName, $parameters);
        }

        return $rendered;
    }

    public function renderSingle(string $name, $groupName, array $parameters = []): string
    {
        $this->eventDispatcher->dispatch(new RenderSingleEvent(
            $this->registry,
            $groupName,
            $name,
            $parameters
        ), RenderSingleEvent::NAME);

        $group = $this->registry->getComponents($groupName);

        if (!isset($group[$name])) {
            throw new InvalidArgumentException('id', sprintf("Can't find Component '%s' in group '%s'", $name, $group));
        }

        return $group[$name]->render($parameters);
    }
}
