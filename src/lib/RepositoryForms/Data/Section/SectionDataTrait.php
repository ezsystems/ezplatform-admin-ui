<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section;

use eZ\Publish\API\Repository\Values\Content\Section;

trait SectionDataTrait
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Section */
    protected $section;

    public function setSection(Section $section): void
    {
        $this->section = $section;
    }

    public function getId()
    {
        return $this->section ? $this->section->id : null;
    }
}
