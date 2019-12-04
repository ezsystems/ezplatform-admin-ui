<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber;

use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ObjectRelationAllowedContentTypes implements EventSubscriberInterface
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
        $context = $event->getContext();
        $config = $event->getConfig();

        if (!in_array($event->getConfigName(), ['single', 'multiple'])) {
            return;
        }

        if (
            !isset($context['type'], $context['allowed_content_types'])
            || 'object_relation' !== $context['type']
        ) {
            return;
        }

        if (!empty($config['content_on_the_fly']['allowed_content_types'])) {
            $config['content_on_the_fly']['allowed_content_types'] = array_values(
                array_intersect(
                    $config['content_on_the_fly']['allowed_content_types'],
                    $context['allowed_content_types']
                )
            );
        } else {
            $config['content_on_the_fly']['allowed_content_types'] = $context['allowed_content_types'];
        }

        $event->setConfig($config);
    }
}
