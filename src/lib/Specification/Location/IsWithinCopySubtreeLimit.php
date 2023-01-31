<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Location;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Filter\Filter;
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
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function isSatisfiedBy($item): bool
    {
        if ($this->copyLimit === -1) {
            return true;
        }

        if ($this->copyLimit === 0) {
            return false;
        }

        if (!$this->isContainer($item)) {
            return true;
        }

        return $this->copyLimit >= $this->getSubtreeSize($item);
    }

    private function getSubtreeSize(Location $location): int
    {
        return $this->locationService->count(
            new Filter(new Criterion\Subtree($location->pathString))
        );
    }

    private function isContainer(Location $location): bool
    {
        return $location->getContentInfo()->getContentType()->isContainer;
    }
}
