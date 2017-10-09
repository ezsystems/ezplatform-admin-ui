<?php

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
