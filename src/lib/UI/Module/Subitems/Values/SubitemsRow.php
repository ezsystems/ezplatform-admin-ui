<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values;

use eZ\Publish\Core\REST\Common\Value as RestValue;
use eZ\Publish\Core\REST\Server\Values\RestContent;
use eZ\Publish\Core\REST\Server\Values\RestLocation;

class SubitemsRow extends RestValue
{
    /** @var RestLocation */
    public $restLocation;

    /** @var RestContent */
    public $restContent;

    /**
     * @param RestLocation $restLocation
     * @param RestContent $restContent
     */
    public function __construct(RestLocation $restLocation, RestContent $restContent)
    {
        $this->restLocation = $restLocation;
        $this->restContent = $restContent;
    }
}
