<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformAdminUi\UI\Service\TabService;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabEvent;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabEvents;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabGroupEvent;
use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use EzSystems\EzPlatformAdminUi\Tab\TabInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class TabExtension extends Twig_Extension
{
    /** @var Environment */
    protected $twig;

    /** @var TabService */
    protected $tabService;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var string */
    protected $defaultTemplate;

    /**
     * @param Environment $twig
     * @param TabService $tabService
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $defaultTemplate
     */
    public function __construct(
        Environment $twig,
        TabService $tabService,
        EventDispatcherInterface $eventDispatcher,
        string $defaultTemplate
    ) {
        $this->twig = $twig;
        $this->tabService = $tabService;
        $this->eventDispatcher = $eventDispatcher;
        $this->defaultTemplate = $defaultTemplate;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'ez_platform_tabs',
                [$this, 'renderTabGroup'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param string $groupIdentifier
     * @param array $parameters
     * @param string|null $template
     *
     * @return string
     */
    public function renderTabGroup(string $groupIdentifier, array $parameters = [], ?string $template = null): string
    {
        $template = $template ?? $this->defaultTemplate;

        $tabGroup = new TabGroup($groupIdentifier);

        $tabGroupEvent = new TabGroupEvent();
        $tabGroupEvent->setData($tabGroup);
        $tabGroupEvent->setParameters($parameters);

        $this->eventDispatcher->dispatch(TabEvents::TAB_GROUP_INITIALIZE, $tabGroupEvent);

        $this->eventDispatcher->dispatch(TabEvents::TAB_GROUP_PRE_RENDER, $tabGroupEvent);

        $tabs = [];
        foreach ($tabGroupEvent->getData()->getTabs() as $tab) {
            $tabEvent = $this->dispatchTabPreRenderEvent($tab, $parameters);
            $tabs[] = $this->composeTabParameters($tabEvent->getData(), $parameters);
        }

        return $this->twig->render(
            $template,
            array_merge(['tabs' => $tabs, 'group' => $groupIdentifier], $parameters)
        );
    }

    /**
     * @param TabInterface $tab
     * @param array $parameters
     *
     * @return TabEvent
     */
    private function dispatchTabPreRenderEvent(TabInterface $tab, array $parameters): TabEvent
    {
        $tabEvent = new TabEvent();
        $tabEvent->setData($tab);
        $tabEvent->setParameters($parameters);

        $this->eventDispatcher->dispatch(TabEvents::TAB_PRE_RENDER, $tabEvent);

        return $tabEvent;
    }

    /**
     * @param TabInterface $tab
     * @param array $parameters
     *
     * @return array
     */
    private function composeTabParameters(TabInterface $tab, array $parameters): array
    {
        return [
            'name' => $tab->getName(),
            'view' => $tab->renderView($parameters),
            'identifier' => $tab->getIdentifier(),
        ];
    }
}
