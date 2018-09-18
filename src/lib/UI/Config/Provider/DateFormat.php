<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use DateTime;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * @todo Will be refactored once DateFormat becomes a User Setting in 2.4
 */
class DateFormat implements ProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function getConfig()
    {
        return DateTime::RFC2822;
    }
}
