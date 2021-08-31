<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\EventListener;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\Form\FormEvent;

class BuildPathFromRootListener
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        LocationService $locationService,
        URLAliasService $urlAliasService,
        ConfigResolverInterface $configResolver
    ) {
        $this->locationService = $locationService;
        $this->urlAliasService = $urlAliasService;
        $this->configResolver = $configResolver;
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function onPreSubmitData(FormEvent $event)
    {
        $data = $event->getData();
        if (!array_key_exists('site_root', $data) || false === (bool)$data['site_root']) {
            $location = $this->locationService->loadLocation((int)$data['location']);
            if (1 >= $location->depth) {
                return;
            }
            $data['path'] = $this->createPathBasedOnParentLocation($location->parentLocationId, $data['path']);
            $event->setData($data);
        } elseif (isset($data['site_root']) && true === (bool)$data['site_root'] && !empty($data['site_access'])) {
            $parameterName = 'content.tree_root.location_id';
            $defaultTreeRootLocationId = $this->configResolver->getParameter($parameterName);
            $treeRootLocationId = $this->configResolver->hasParameter($parameterName, null, $data['site_access'])
                ? $this->configResolver->getParameter($parameterName, null, $data['site_access'])
                : $defaultTreeRootLocationId;

            $data['path'] = $this->createPathBasedOnParentLocation((int)$treeRootLocationId, $data['path']);
            $event->setData($data);
        }
    }

    private function createPathBasedOnParentLocation(int $locationId, string $path): string
    {
        $parentLocation = $this->locationService->loadLocation($locationId);
        $urlAlias = $this->urlAliasService->reverseLookup($parentLocation);

        return $urlAlias->path . '/' . $path;
    }
}
