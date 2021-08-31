<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
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

        if (!empty($config['allowed_content_types'])) {
            $intersection = array_values(
                array_intersect(
                    $config['allowed_content_types'],
                    $context['allowed_content_types']
                )
            );

            $config['allowed_content_types'] = empty($intersection)
                ? null
                : $intersection;
        } else {
            $config['allowed_content_types'] = empty($context['allowed_content_types'])
                ? null
                : $context['allowed_content_types'];
        }

        $event->setConfig($config);
    }
}
