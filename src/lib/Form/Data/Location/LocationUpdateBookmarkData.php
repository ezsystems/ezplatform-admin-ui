<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Validator\Constraints as Assert;

class LocationUpdateBookmarkData
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location|null
     * @Assert\NotNull()
     */
    private $location;

    /** @var bool|null */
    private $bookmarked;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param bool $bookmarked
     */
    public function __construct(?Location $location = null, bool $bookmarked = false)
    {
        if (null === $location) {
            return;
        }

        $this->location = $location;
        $this->bookmarked = $bookmarked;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     */
    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return bool|null
     */
    public function isBookmarked(): ?bool
    {
        return $this->bookmarked;
    }

    /**
     * @param bool|null $bookmarked
     */
    public function setBookmarked(?bool $bookmarked): void
    {
        $this->bookmarked = $bookmarked;
    }
}
