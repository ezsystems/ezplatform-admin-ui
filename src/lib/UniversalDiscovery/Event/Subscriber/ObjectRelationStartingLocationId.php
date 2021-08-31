<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber;

use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ObjectRelationStartingLocationId implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigResolveEvent::NAME => ['onUdwConfigResolve'],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent $event
     */
    public function onUdwConfigResolve(ConfigResolveEvent $event): void
    {
        $configName = $event->getConfigName();
        if ('single' !== $configName && 'multiple' !== $configName) {
            return;
        }

        $context = $event->getContext();
        if (
            !isset($context['type'])
            || 'object_relation' !== $context['type']
            || !isset($context['starting_location_id'])
        ) {
            return;
        }

        $config = $event->getConfig();
        $config['starting_location_id'] = $context['starting_location_id'];

        $event->setConfig($config);
    }
}
