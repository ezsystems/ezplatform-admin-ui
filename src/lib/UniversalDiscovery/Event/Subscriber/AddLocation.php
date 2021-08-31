<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber;

use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddLocation implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigResolveEvent::NAME => ['onUdwConfigResolve', -10],
        ];
    }

    public function onUdwConfigResolve(ConfigResolveEvent $event): void
    {
        if ($event->getConfigName() !== 'add_location') {
            return;
        }

        $config = $event->getConfig();
        $config['allowed_content_types'] = null;

        $event->setConfig($config);
    }
}
