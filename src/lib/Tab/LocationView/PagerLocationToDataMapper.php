<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;
use EzSystems\EzPlatformAdminUi\UI\Value;
use Pagerfanta\Pagerfanta;

class PagerLocationToDataMapper
{
    /** @var ValueFactory */
    protected $valueFactory;

    public function __construct(
        ValueFactory $valueFactory
    ) {
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param \Pagerfanta\Pagerfanta $pager
     *
     * @return array
     */
    public function map(Pagerfanta $pager): array
    {
        $locations = [];
        foreach ($pager as $location) {
            $locations[] = $location;
        }

        $data = array_map(
            [$this->valueFactory, 'createLocation'],
            $locations
        );
        $data = $this->prioritizeMainLocation($data);

        return $data;
    }

    /**
     * @param Location[] $locations
     *
     * @return Value\Content\Location[]
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
}
