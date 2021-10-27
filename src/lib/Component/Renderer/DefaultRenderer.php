<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Component\Renderer;

use Ibexa\AdminUi\Component\Event\RenderGroupEvent;
use Ibexa\AdminUi\Component\Event\RenderSingleEvent;
use Ibexa\AdminUi\Component\Registry;
use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\Contracts\AdminUi\Component\Renderer\RendererInterface;
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

class_alias(DefaultRenderer::class, 'EzSystems\EzPlatformAdminUi\Component\Renderer\DefaultRenderer');
