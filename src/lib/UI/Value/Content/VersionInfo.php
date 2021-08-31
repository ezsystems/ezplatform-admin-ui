<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\Content;

use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo as CoreVersionInfo;

/**
 * Extends original value object in order to provide additional fields.
 * Takes a standard VersionInfo instance and retrieves properties from it in addition to the provided properties.
 */
class VersionInfo extends CoreVersionInfo
{
    /** @var \eZ\Publish\API\Repository\Values\User\User */
    protected $author;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Language[]
     */
    protected $translations;

    /**
     * User can remove.
     *
     * @var bool
     */
    protected $userCanRemove;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param array $properties
     */
    public function __construct(APIVersionInfo $versionInfo, array $properties = [])
    {
        parent::__construct(get_object_vars($versionInfo) + $properties);
    }

    /**
     * Can delete version.
     *
     * @return bool
     */
    public function canDelete(): bool
    {
        return $this->userCanRemove;
    }
}
