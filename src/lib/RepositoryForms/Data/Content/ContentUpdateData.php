<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content;

use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessCheckable;

/**
 * @property \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData[] $fieldsData
 * @property \eZ\Publish\API\Repository\Values\Content\Content $contentDraft
 */
class ContentUpdateData extends ContentUpdateStruct implements NewnessCheckable
{
    use ContentData;

    protected $contentDraft;

    public function isNew(): bool
    {
        return false;
    }
}
