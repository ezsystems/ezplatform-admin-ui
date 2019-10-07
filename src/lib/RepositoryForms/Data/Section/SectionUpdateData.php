<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section;

use eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessChecker;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessCheckable;

/**
 * @property \eZ\Publish\API\Repository\Values\Content\Section $section
 */
class SectionUpdateData extends SectionUpdateStruct implements NewnessCheckable
{
    use SectionDataTrait;
    use NewnessChecker;

    /**
     * Returns the value of the property which can be considered as the value object identifier.
     *
     * @return string
     */
    protected function getIdentifierValue()
    {
        return $this->section->identifier;
    }
}
