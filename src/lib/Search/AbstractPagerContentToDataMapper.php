<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Search;

use function class_alias;

class_alias(
    \EzSystems\EzPlatformAdminUi\Pagination\Mapper\AbstractPagerContentToDataMapper::class,
    __NAMESPACE__ . '\AbstractPagerContentToDataMapper'
);

if (false) {
    /**
     * @deprecated since 3.1, to be removed in 3.2.
     * Use \EzSystems\EzPlatformAdminUi\Pagination\Mapper\AbstractPagerContentToDataMapper instead
     */
    abstract class AbstractPagerContentToDataMapper extends \EzSystems\EzPlatformAdminUi\Pagination\Mapper\AbstractPagerContentToDataMapper
    {
    }
}
