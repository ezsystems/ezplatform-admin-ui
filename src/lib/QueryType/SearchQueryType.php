<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\QueryType;

use function class_alias;

class_alias(
    \Ibexa\Platform\Search\QueryType\SearchQueryType::class,
    __NAMESPACE__ . '\SearchQueryType'
);

if (false) {
    /**
     * @deprecated since 3.1, to be removed in 3.2.
     * Use \Ibexa\Platform\Search\QueryType\SearchQueryType instead
     */
    class SearchQueryType extends \Ibexa\Platform\Search\QueryType\SearchQueryType
    {
    }
}
