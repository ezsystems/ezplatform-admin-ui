<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Section;

use eZ\Publish\API\Repository\Values\Content\Section;

/**
 * @todo add validation
 */
class SectionDeleteData
{
    /** @var Section|null */
    protected $section;

    /**
     * @param Section|null $section
     */
    public function __construct(?Section $section = null)
    {
        $this->section = $section;
    }

    /**
     * @return Section|null
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @param Section|null $section
     */
    public function setSection(?Section $section)
    {
        $this->section = $section;
    }
}
