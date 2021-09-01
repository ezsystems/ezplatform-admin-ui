<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Tab\Event\Subscriber;

use Ibexa\AdminUi\Tab\Event\TabEvents;
use Ibexa\AdminUi\Tab\Event\TabGroupEvent;
use Ibexa\AdminUi\UI\Service\TabService;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Evaluates if tabs should be visible (Tabs implementing ConditionalTabInterface).
 *
 * @see ConditionalTabInterface
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
     * @param \EzSystems\EzPlatformAdminUi\Tab\Event\TabGroupEvent $tabGroupEvent
     */
    public function onTabGroupInitialize(TabGroupEvent $tabGroupEvent)
    {
        $tabGroup = $tabGroupEvent->getData();
        $tabGroupIdentifier = $tabGroupEvent->getData()->getIdentifier();
        $parameters = $tabGroupEvent->getParameters();
        try {
            $tabs = $this->tabService->getTabGroup($tabGroupIdentifier)->getTabs();
        } catch (InvalidArgumentException $e) {
            $tabs = [];
        }

        foreach ($tabs as $tab) {
            if (!$tab instanceof ConditionalTabInterface || $tab->evaluate($parameters)) {
                $tabGroup->addTab($tab);
            }
        }

        $tabGroupEvent->setData($tabGroup);
    }
}

class_alias(ConditionalTabSubscriber::class, 'EzSystems\EzPlatformAdminUi\Tab\Event\Subscriber\ConditionalTabSubscriber');
