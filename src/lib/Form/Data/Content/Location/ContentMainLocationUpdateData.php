<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Location;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo Add validation.
 */
class ContentMainLocationUpdateData
{
    /**
     * @Assert\NotBlank()
     *
     * @var ContentInfo|null
     */
    public $contentInfo;

    /**
     * @todo add more validation constraints
     *
     * @Assert\NotBlank()
     *
     * @var Location
     */
    public $location;

    /**
     * @param ContentInfo|null $contentInfo
     * @param Location|null $location
     */
    public function __construct(
        ContentInfo $contentInfo = null,
        Location $location = null
    ) {
        $this->contentInfo = $contentInfo;
        $this->location = $location;
    }

    /**
     * @return ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param ContentInfo|null $contentInfo
     */
    public function setContentInfo(?ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }
}
