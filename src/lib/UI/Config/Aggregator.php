<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\UI\Config;

use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;

/**
 * Aggregates a set of ApplicationConfig Providers.
 */
class Aggregator
{
    /** @var \Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface[] ApplicationConfigProviders, indexed by namespace string */
    protected $providers;

    /**
     * Aggregator constructor.
     *
     * @param \Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * Adds an Provider to the aggregator.
     *
     * @param string $key
     * @param \Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface $provider
     */
    public function addProvider(string $key, ProviderInterface $provider)
    {
        $this->providers[$key] = $provider;
    }

    /**
     * @param string $key
     *
     * @return \Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function removeProvider(string $key): ProviderInterface
    {
        if (!isset($this->providers[$key])) {
            throw new InvalidArgumentException('key', sprintf('Provider under key "%s" not found', $key));
        }

        return $this->providers[$key];
    }

    /**
     * @return \Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @param \Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface[] $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = [];
        foreach ($this->providers as $key => $provider) {
            $config[$key] = $provider->getConfig();
        }

        return $config;
    }
}

class_alias(Aggregator::class, 'EzSystems\EzPlatformAdminUi\UI\Config\Aggregator');
