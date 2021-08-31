<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config;

/**
 * Provides parameters as a serializable value.
 */
interface ProviderInterface
{
    /**
     * @return mixed Anything that is serializable via json_encode()
     */
    public function getConfig();
}
