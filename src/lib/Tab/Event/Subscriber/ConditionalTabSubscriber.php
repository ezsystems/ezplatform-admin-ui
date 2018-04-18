<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Event\Subscriber;

use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabEvents;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabGroupEvent;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Service\TabService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Reorders tabs according to their Order value (Tabs implementing OrderedTabInterface).
 * Tabs without order specified are pushed to the end of the group.
 *
 * @see OrderedTabInterface
 */
class ConditionalTabSubscriber implements EventSubscriberInterface
{
    /**
     * @var \EzSystems\EzPlatformAdminUi\UI\Service\TabService
     */
    private $tabService;

    public function __construct(TabService $tabService)
    {
        $this->tabService = $tabService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TabEvents::TAB_GROUP_INITIALIZE => ['onTabGroupInitialize'],
        ];
    }

    /**
     * @param TabGroupEvent $tabGroupEvent
     */
    public function onTabGroupInitialize(TabGroupEvent $tabGroupEvent)
    {
        $tabGroup = $tabGroupEvent->getData();
        $tabGroupIdentifier = $tabGroupEvent->getData()->getIdentifier();
        $parameters = $tabGroupEvent->getParameters();
        $tabs = $this->tabService->getTabGroup($tabGroupIdentifier)->getTabs();

        foreach ($tabs as $tab) {
            if (!$tab instanceof ConditionalTabInterface || $tab->evaluate($parameters)) {
                $tabGroup->addTab($tab);
            }
        }

        $tabGroupEvent->setData($tabGroup);
    }
}
