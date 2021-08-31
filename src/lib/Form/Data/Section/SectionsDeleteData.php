<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Section;

/**
 * @todo Add validation
 */
class SectionsDeleteData
{
    /** @var array|null */
    protected $sections;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section[]|null $sections
     */
    public function __construct(array $sections = [])
    {
        $this->sections = $sections;
    }

    /**
     * @return array|null
     */
    public function getSections(): ?array
    {
        return $this->sections;
    }

    /**
     * @param array|null $sections
     */
    public function setSections(?array $sections)
    {
        $this->sections = $sections;
    }
}
