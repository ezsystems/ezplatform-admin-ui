<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Specification\Location;

use Ibexa\AdminUi\Specification\AbstractSpecification;

class IsContainer extends AbstractSpecification
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $item
     *
     * @return bool
     */
    public function isSatisfiedBy($item): bool
    {
        return $item->getContent()->getContentType()->isContainer;
    }
}

class_alias(IsContainer::class, 'EzSystems\EzPlatformAdminUi\Specification\Location\IsContainer');
