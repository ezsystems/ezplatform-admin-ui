<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Specification;

use eZ\Publish\API\Repository\Values\Content\Content;

interface ContentSpecification
{
    /**
     * Check to see if the specification is satisfied.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return bool
     */
    public function isSatisfiedBy(Content $content): bool;
}
