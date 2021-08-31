<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Action;

use Symfony\Component\EventDispatcher as SymfonyEventDispatcher;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(SymfonyEventDispatcher\EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UI\Action\UiActionEventInterface $event
     */
    public function dispatch(UiActionEventInterface $event): void
    {
        $action = $event->getName();

        $this->eventDispatcher->dispatch($event, EventDispatcherInterface::EVENT_NAME_PREFIX);
        $this->eventDispatcher->dispatch($event, sprintf('%s.%s', EventDispatcherInterface::EVENT_NAME_PREFIX, $action));
        $this->eventDispatcher->dispatch($event, sprintf('%s.%s.%s', EventDispatcherInterface::EVENT_NAME_PREFIX, $action, $event->getType()));
    }
}
