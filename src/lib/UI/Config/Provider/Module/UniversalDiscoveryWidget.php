<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\Module;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Provides information about current user with resolved profile picture.
 */
class UniversalDiscoveryWidget implements ProviderInterface
{
    /** @var ConfigResolverInterface */
    private $configResolver;

    /**
     * @param ConfigResolverInterface $configResolver
     */
    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        /* config structure has to reflect UDW module's config structure */
        return [
            'startingLocationId' => $this->configResolver->getParameter(
                'universal_discovery_widget_module.default_location_id'
            ),
        ];
    }
}
