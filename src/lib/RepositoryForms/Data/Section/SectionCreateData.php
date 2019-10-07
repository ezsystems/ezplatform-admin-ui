<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section;

use eZ\Publish\API\Repository\Values\Content\SectionCreateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessCheckable;

/**
 * @property \eZ\Publish\API\Repository\Values\Content\Section $section
 */
class SectionCreateData extends SectionCreateStruct implements NewnessCheckable
{
    use SectionDataTrait;

    public function isNew(): bool
    {
        return true;
    }
}
