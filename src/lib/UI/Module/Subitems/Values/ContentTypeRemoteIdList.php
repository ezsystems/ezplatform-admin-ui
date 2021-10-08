<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\UI\Module\Subitems\Values;

use EzSystems\EzPlatformRest\Value as RestValue;

class ContentTypeRemoteIdList extends RestValue
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType[] */
    public $contentTypes;

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType[] $contentTypes
     */
    public function __construct(array $contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }
}

class_alias(ContentTypeRemoteIdList::class, 'EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\ContentTypeRemoteIdList');
