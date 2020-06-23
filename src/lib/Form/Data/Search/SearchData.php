<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Search;

use function class_alias;

class_alias(
    \Ibexa\Platform\Bundle\SearchBundle\Form\Data\SearchData::class,
    __NAMESPACE__ . '\SearchData'
);

if (false) {
    /**
     * @deprecated since 3.1, to be removed in 3.2.
     * Use \Ibexa\Platform\Bundle\SearchBundle\Form\Data\SearchData instead
     */
    class SearchData extends \Ibexa\Platform\Bundle\SearchBundle\Form\Data\SearchData
    {
    }
}
