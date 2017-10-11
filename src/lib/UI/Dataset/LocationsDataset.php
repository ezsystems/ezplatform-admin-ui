<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class LocationsDataset
{
    /** @var LocationService */
    protected $locationService;

    /** @var ValueFactory */
    protected $valueFactory;

    /** @var UIValue\Content\Location[] */
    protected $data;

    /**
     * @param LocationService $locationService
     * @param ValueFactory $valueFactory
     */
    public function __construct(LocationService $locationService, ValueFactory $valueFactory)
    {
        $this->locationService = $locationService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param ContentInfo $contentInfo
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
     * @param Location[] $locations
     *
     * @return UIValue\Content\Location[]
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
     * @return UIValue\Content\Location[]
     */
    public function getLocations(): array
    {
        return $this->data;
    }
}
