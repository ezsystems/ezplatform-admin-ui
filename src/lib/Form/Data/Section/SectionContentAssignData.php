<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Section;

use eZ\Publish\API\Repository\Values\Content\Section;

/**
 * @todo add validation
 */
class SectionContentAssignData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Section|null */
    protected $section;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location[] */
    protected $locations;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section|null $section
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $locations
     */
    public function __construct(?Section $section = null, array $locations = [])
    {
        $this->section = $section;
        $this->locations = $locations;
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
    public function setSection(?Section $section)
    {
        $this->section = $section;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $locations
     */
    public function setLocations(array $locations)
    {
        $this->locations = $locations;
    }
}
