<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroupCreateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessCheckable;

/**
 * @property \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
 */
class ContentTypeGroupCreateData extends ContentTypeGroupCreateStruct implements NewnessCheckable
{
    use ContentTypeGroupDataTrait;

    public function isNew(): bool
    {
        return true;
    }
}
