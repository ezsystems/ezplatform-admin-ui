<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class LocationsDataset
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    protected $valueFactory;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location[] */
    protected $data;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(LocationService $locationService, ValueFactory $valueFactory)
    {
        $this->locationService = $locationService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return LocationsDataset
     */
    public function load(ContentInfo $contentInfo): self
    {
        $this->data = array_map(
            [$this->valueFactory, 'createLocation'],
            $this->locationService->loadLocations($contentInfo)
        );
        $this->data = $this->prioritizeMainLocation($this->data);

        return $this;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $locations
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    protected function prioritizeMainLocation(array $locations): array
    {
        foreach ($locations as $key => $location) {
            if ($location->main) {
                unset($locations[$key]);
                array_unshift($locations, $location);
                break;
            }
        }

        return $locations;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    public function getLocations(): array
    {
        return $this->data;
    }
}
