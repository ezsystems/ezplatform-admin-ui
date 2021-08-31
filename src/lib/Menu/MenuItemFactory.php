<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Menu;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuItemFactory implements FactoryInterface
{
    /** @var \Knp\Menu\FactoryInterface */
    protected $factory;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(
        FactoryInterface $factory,
        PermissionResolver $permissionResolver,
        LocationService $locationService
    ) {
        $this->factory = $factory;
        $this->permissionResolver = $permissionResolver;
        $this->locationService = $locationService;
    }

    /**
     * Creates Location menu item only when user has content:read permission.
     *
     * @param string $name
     * @param int $locationId
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface|null
     */
    public function createLocationMenuItem(string $name, int $locationId, array $options = []): ?ItemInterface
    {
        try {
            $location = $this->locationService->loadLocation($locationId);
            $contentInfo = $location->getContentInfo();
            $canRead = $this->permissionResolver->canUser('content', 'read', $contentInfo);
        } catch (\Exception $e) {
            return null;
        }

        $defaults = [
            'route' => '_ez_content_view',
            'routeParameters' => [
                'contentId' => $contentInfo->id,
                'locationId' => $locationId,
            ],
        ];

        return $this->createItem($name, array_merge_recursive($defaults, $options));
    }

    public function createItem($name, array $options = []): ItemInterface
    {
        $defaults = [
            'extras' => ['translation_domain' => 'menu'],
        ];

        return $this->factory->createItem($name, array_merge_recursive($defaults, $options));
    }
}
