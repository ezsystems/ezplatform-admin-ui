<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\Module;

use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver;

/**
 * Provides information about the id of starting Location for the Universal Discovery Widget.
 */
class UniversalDiscoveryWidget implements ProviderInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver */
    private $configResolver;

    /**
     * @param \EzSystems\EzPlatformAdminUi\UniversalDiscovery\ConfigResolver $configResolver
     */
    public function __construct(
        ConfigResolver $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function getConfig(): array
    {
        /* config structure has to reflect UDW module's config structure */
        return [
            'startingLocationId' => $this->getStartingLocationId(),
        ];
    }

    protected function getStartingLocationId(): ?int
    {
        return $this->configResolver->getConfig(ConfigResolver::DEFAULT_CONFIGURATION_KEY)['starting_location_id'];
    }
}
