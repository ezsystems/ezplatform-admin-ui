<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\Module;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides information about current setting for Universal Discovery Widget.
 */
class UniversalDiscoveryWidget implements ProviderInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        LanguageService $languageService,
        ContentTypeService $contentTypeService,
        RequestStack $requestStack
    ) {
        $this->configResolver = $configResolver;
        $this->languageService = $languageService;
        $this->contentTypeService = $contentTypeService;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        /* config structure has to reflect UDW module's config structure */
        return [
            'startingLocationId' => $this->getStartingLocationId(),
            'currentLocationId' => $this->getCurrentLocationId(),
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

    /**
     * @return int|null
     */
    protected function getCurrentLocationId(): ?int
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request ? (int)$request->get('locationId') : null;
    }
}
