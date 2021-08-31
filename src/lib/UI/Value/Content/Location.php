<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\Content;

use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\Core\Repository\Values\Content\Location as CoreLocation;

/**
 * Extends original value object in order to provide additional fields.
 * Takes a standard location instance and retrieves properties from it in addition to the provided properties.
 */
class Location extends CoreLocation
{
    /**
     * Child count.
     *
     * @var int
     */
    protected $childCount;

    /**
     * Is main location.
     *
     * @var bool
     */
    protected $main;

    /**
     * Path locations.
     *
     * @var \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    protected $pathLocations;

    /**
     * User can manage.
     *
     * @var bool
     */
    protected $userCanManage;

    /**
     * User can remove.
     *
     * @var bool
     */
    protected $userCanRemove;

    /**
     * User can edit.
     *
     * @var bool
     */
    protected $userCanEdit;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param array $properties
     */
    public function __construct(APILocation $location, array $properties = [])
    {
        parent::__construct(get_object_vars($location) + $properties);
    }

    /**
     * Can delete location.
     *
     * @return bool
     */
    public function canDelete(): bool
    {
        return !$this->main && $this->userCanManage && $this->userCanRemove;
    }

    /**
     * Can edit location.
     *
     * @return bool
     */
    public function canEdit(): bool
    {
        return $this->userCanEdit;
    }
}
