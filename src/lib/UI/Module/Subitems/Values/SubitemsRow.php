<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values;

use EzSystems\EzPlatformRest\Server\Values\RestContent;
use EzSystems\EzPlatformRest\Server\Values\RestLocation;
use EzSystems\EzPlatformRest\Value as RestValue;

class SubitemsRow extends RestValue
{
    /** @var \EzSystems\EzPlatformRest\Server\Values\RestLocation */
    public $restLocation;

    /** @var \EzSystems\EzPlatformRest\Server\Values\RestContent */
    public $restContent;

    /**
     * @param \EzSystems\EzPlatformRest\Server\Values\RestLocation $restLocation
     * @param \EzSystems\EzPlatformRest\Server\Values\RestContent $restContent
     */
    public function __construct(RestLocation $restLocation, RestContent $restContent)
    {
        $this->restLocation = $restLocation;
        $this->restContent = $restContent;
    }
}
