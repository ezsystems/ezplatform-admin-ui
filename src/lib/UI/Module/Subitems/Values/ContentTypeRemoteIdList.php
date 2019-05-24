<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformRest\Value as RestValue;

class ContentTypeRemoteIdList extends RestValue
{
    /** @var ContentType[] */
    public $contentTypes;

    /**
     * @param ContentType[] $contentTypes
     */
    public function __construct(array $contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }
}
