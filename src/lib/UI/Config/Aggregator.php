<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\UI\Config;

use EzPlatformAdminUi\Exception\InvalidArgumentException;

/**
 * Aggregates a set of ApplicationConfig Providers.
 */
class Aggregator
{
    /** @var ProviderInterface[] ApplicationConfigProviders, indexed by namespace string */
    protected $providers;

    /**
     * Aggregator constructor.
     *
     * @param ProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * Adds an Provider to the aggregator.
     *
     * @param string $key
     * @param ProviderInterface $provider
     */
    public function addProvider(string $key, ProviderInterface $provider)
    {
        $this->providers[$key] = $provider;
    }

    /**
     * @param string $key
     *
     * @return ProviderInterface
     * @throws InvalidArgumentException
     */
    public function removeProvider(string $key)
    {
        if (!isset($this->providers[$key])) {
            throw new InvalidArgumentException('key', sprintf('Provider under key "%s" not found', $key));
        }

        return $this->providers[$key];
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @param ProviderInterface[] $providers
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
