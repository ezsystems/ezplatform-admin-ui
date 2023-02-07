<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Location;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;

/**
 * @internal
 */
class IsWithinCopySubtreeLimit extends AbstractSpecification
{
    /** @var int */
    private $copyLimit;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    public function __construct(
        int $copyLimit,
        LocationService $locationService
    ) {
        $this->copyLimit = $copyLimit;
        $this->locationService = $locationService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $item
     */
    public function isSatisfiedBy($item): bool
    {
        if ($this->copyLimit === -1) {
            return true;
        }

        if ($this->copyLimit === 0 || !$this->isContainer($item)) {
            return false;
        }

        return $this->copyLimit >= $this->locationService->getSubtreeSize($item);
    }

    private function isContainer(Location $location): bool
    {
        return $location->getContentInfo()->getContentType()->isContainer();
    }
}
