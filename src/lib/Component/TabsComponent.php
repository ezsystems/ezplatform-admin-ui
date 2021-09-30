<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Component;

use Ibexa\AdminUi\Tab\Event\TabEvent;
use Ibexa\AdminUi\Tab\Event\TabEvents;
use Ibexa\AdminUi\Tab\Event\TabGroupEvent;
use Ibexa\AdminUi\Tab\TabGroup;
use Ibexa\Contracts\AdminUi\Component\Renderable;
use Ibexa\Contracts\AdminUi\Tab\TabInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

class TabsComponent implements Renderable
{
    /** @var \Twig\Environment */
    protected $twig;

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var string */
    protected $template;

    /** @var string */
    protected $groupIdentifier;

    /** @var array */
    protected $parameters;

    public function __construct(
        Environment $twig,
        EventDispatcherInterface $eventDispatcher,
        string $template,
        string $groupIdentifier,
        array $parameters = []
    ) {
        $this->twig = $twig;
        $this->eventDispatcher = $eventDispatcher;
        $this->template = $template;
        $this->groupIdentifier = $groupIdentifier;
        $this->parameters = $parameters;
    }

    public function render(array $parameters = []): string
    {
        $tabGroup = new TabGroup($this->groupIdentifier);

        $tabGroupEvent = new TabGroupEvent();
        $tabGroupEvent->setData($tabGroup);
        $tabGroupEvent->setParameters($parameters);

        $this->eventDispatcher->dispatch($tabGroupEvent, TabEvents::TAB_GROUP_INITIALIZE);

        $this->eventDispatcher->dispatch($tabGroupEvent, TabEvents::TAB_GROUP_PRE_RENDER);

        $tabs = [];
        foreach ($tabGroupEvent->getData()->getTabs() as $tab) {
            $tabEvent = $this->dispatchTabPreRenderEvent($tab, $parameters);
            $parameters = array_merge($parameters, $tabGroupEvent->getParameters(), $tabEvent->getParameters());
            $tabs[] = $this->composeTabParameters($tabEvent->getData(), $parameters);
        }

        return $this->twig->render(
            $this->template,
            array_merge($this->parameters, $parameters, ['tabs' => $tabs, 'group' => $this->groupIdentifier])
        );
    }

    private function dispatchTabPreRenderEvent(TabInterface $tab, array $parameters): TabEvent
    {
        $tabEvent = new TabEvent();
        $tabEvent->setData($tab);
        $tabEvent->setParameters($parameters);

        $this->eventDispatcher->dispatch($tabEvent, TabEvents::TAB_PRE_RENDER);

        return $tabEvent;
    }

    private function composeTabParameters(TabInterface $tab, array $parameters): array
    {
        return [
            'name' => $tab->getName(),
            'view' => $tab->renderView($parameters),
            'identifier' => $tab->getIdentifier(),
        ];
    }
}

class_alias(TabsComponent::class, 'EzSystems\EzPlatformAdminUi\Component\TabsComponent');
