<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroupUpdateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessChecker;

/**
 * @property \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
 */
class ContentTypeGroupUpdateData extends ContentTypeGroupUpdateStruct
{
    use ContentTypeGroupDataTrait;
    use NewnessChecker;

    protected function getIdentifierValue()
    {
        return $this->contentTypeGroup->identifier;
    }
}
