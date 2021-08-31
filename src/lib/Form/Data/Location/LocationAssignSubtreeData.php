<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Location;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Section;
use Symfony\Component\Validator\Constraints as Assert;

class LocationAssignSubtreeData
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Section|null
     *
     * @Assert\NotBlank()
     */
    protected $section;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location|null
     *
     * @Assert\NotBlank()
     */
    protected $location;

    public function __construct(?Section $section = null, ?Location $location = null)
    {
        $this->section = $section;
        $this->location = $location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Section|null
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section|null $section
     */
    public function setSection(?Section $section): void
    {
        $this->section = $section;
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
}
