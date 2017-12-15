<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\REST\Common\Value as RestValue;

class SubitemsRow extends RestValue
{
    /** @var Location */
    public $location;

    /** @var Content */
    public $content;

    /**
     * @param Location $location
     * @param Content $content
     */
    public function __construct(Location $location, Content $content)
    {
        $this->location = $location;
        $this->content = $content;
    }
}
