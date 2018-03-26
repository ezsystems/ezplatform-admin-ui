<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\Module;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Provides information about current user with resolved profile picture.
 */
class UniversalDiscoveryWidget implements ProviderInterface
{
    /** @var ConfigResolverInterface */
    private $configResolver;

    /** @var LanguageService */
    private $languageService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /**
     * @param ConfigResolverInterface $configResolver
     * @param LanguageService $languageService
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        LanguageService $languageService,
        ContentTypeService $contentTypeService
    ) {
        $this->configResolver = $configResolver;
        $this->languageService = $languageService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        /* config structure has to reflect UDW module's config structure */
        return [
            'startingLocationId' => $this->getStartingLocationId(),
        ];
    }

    /**
     * @return int|null
     */
    protected function getStartingLocationId(): ?int
    {
        return $this->configResolver->getParameter(
            'universal_discovery_widget_module.default_location_id'
        );
    }
}
