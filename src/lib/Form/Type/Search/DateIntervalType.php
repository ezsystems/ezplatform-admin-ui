<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Search;

use function class_alias;

class_alias(
    \EzSystems\EzPlatformAdminUi\Form\Type\Date\DateIntervalType::class,
    __NAMESPACE__ . '\DateIntervalType'
);

if (false) {
    /**
     * @deprecated since 3.1, to be removed in 3.2.
     * Use \EzSystems\EzPlatformAdminUi\Form\Type\Date\DateIntervalType instead
     */
    class DateIntervalType extends \EzSystems\EzPlatformAdminUi\Form\Type\Date\DateIntervalType
    {
    }
}
