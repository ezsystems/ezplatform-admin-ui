<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Location;

use eZ\Publish\API\Repository\LocationService;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;

class HasChildren extends AbstractSpecification
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $item
     *
     * @return bool
     */
    public function isSatisfiedBy($item): bool
    {
        $childCount = $this->locationService->getLocationChildCount($item);

        return 0 < $childCount;
    }
}
