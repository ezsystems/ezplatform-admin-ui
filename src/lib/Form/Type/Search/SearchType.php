<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Search;

use function class_alias;

class_alias(
    \Ibexa\Platform\Bundle\SearchBundle\Form\Type\SearchType::class,
    __NAMESPACE__ . '\SearchType'
);

if (false) {
    /**
     * @deprecated since 3.1, to be removed in 3.2.
     * Use \Ibexa\Platform\Bundle\SearchBundle\Form\Type\SearchType instead
     */
    class SearchType extends \Ibexa\Platform\Bundle\SearchBundle\Form\Type\SearchType
    {
    }
}
