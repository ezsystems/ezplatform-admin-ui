<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;
use EzSystems\EzPlatformAdminUi\UI\Value;
use Pagerfanta\Pagerfanta;

final class PagerLocationToDataMapper
{
    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    public function __construct(
        ValueFactory $valueFactory
    ) {
        $this->valueFactory = $valueFactory;
    }

    /**
     * @return Value\Content\Location[]
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

        return $this->prioritizeMainLocation($data);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $locations
     *
     * @return Value\Content\Location[]
     */
    private function prioritizeMainLocation(array $locations): array
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
