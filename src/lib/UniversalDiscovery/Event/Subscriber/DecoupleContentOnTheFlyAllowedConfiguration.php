<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber;

use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DecoupleContentOnTheFlyAllowedConfiguration implements EventSubscriberInterface
{
    // We need to wait a bit for other subscribers to populate and modify COTF configuration
    private const PRIORITY = -25;

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigResolveEvent::NAME => ['onUdwConfigResolve', self::PRIORITY],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent $event
     */
    public function onUdwConfigResolve(ConfigResolveEvent $event)
    {
        $config = $event->getConfig();

        $config['allowed_content_types'] = $config['allowed_content_types'] ?? $config['content_on_the_fly']['allowed_content_types'];

        $event->setConfig($config);
    }
}