<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConfigResolver
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcher */
    protected $eventDispatcher;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    /** @var array */
    protected $udwConfiguration;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     * @param array $udwConfiguration
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        EventDispatcher $eventDispatcher,
        array $udwConfiguration
    ) {
        $this->configResolver = $configResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->udwConfiguration = $udwConfiguration;
    }

    /**
     * @param string $configName
     * @param array $context
     *
     * @return array
     */
    public function getConfig(string $configName, array $context = []): array
    {
        $config = $this->udwConfiguration[$configName] ?? [];
        $defaults = $this->udwConfiguration['default'] ?? [];

        $config = $this->mergeConfiguration($defaults, $config);

        $configResolveEvent = new ConfigResolveEvent();

        $configResolveEvent->setConfigName($configName);
        $configResolveEvent->setContext($context);
        $configResolveEvent->setConfig($config);

        /** @var ConfigResolveEvent $event */
        $event = $this->eventDispatcher->dispatch(ConfigResolveEvent::NAME, $configResolveEvent);

        return $event->getConfig();
    }

    /**
     * @param array $default
     * @param mixed $apply
     *
     * @return array
     */
    protected function mergeConfiguration(array $default, $apply): array
    {
        foreach ($apply as $key => $item) {
            if (isset($default[$key]) && $this->is_assoc_array($default[$key])) {
                $default[$key] = $this->mergeConfiguration($default[$key], $item);
            } else {
                $default[$key] = $item;
            }
        }

        return $default;
    }

    /**
     * Checks if item is associative array type.
     *
     * @param mixed $item
     *
     * @return bool
     */
    private function is_assoc_array($item): bool
    {
        if (!is_array($item)) {
            // Is not an array at all
            return false;
        }

        if ($item === []) {
            // Treat empty array as Sequential
            return false;
        }

        // Check if keys are equal to sequence of 0 .. n-1
        return array_keys($item) !== range(0, count($item) - 1);
    }
}
