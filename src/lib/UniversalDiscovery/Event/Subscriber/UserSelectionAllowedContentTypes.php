<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\Subscriber;

use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSelectionAllowedContentTypes implements EventSubscriberInterface
{
    /** @var array */
    private $userContentTypeIdentifier;

    /**
     * @param array $userContentTypeIdentifier
     */
    public function __construct(array $userContentTypeIdentifier)
    {
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
    }

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
        $config = $event->getConfig();

        if (!in_array($event->getConfigName(), ['single_user', 'multiple_user'])) {
            return;
        }

        $config['allowed_content_types'] = $this->userContentTypeIdentifier;

        $event->setConfig($config);
    }
}
