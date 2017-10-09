<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\Content;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo as CoreVersionInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;

/**
 * Extends original value object in order to provide additional fields.
 * Takes a standard VersionInfo instance and retrieves properties from it in addition to the provided properties.
 */
class VersionInfo extends CoreVersionInfo
{
    /**
     * @var User
     */
    protected $author;

    /**
     * @var Language[]
     */
    protected $translations;

    /**
     * @param APIVersionInfo $versionInfo
     * @param array $properties
     */
    public function __construct(APIVersionInfo $versionInfo, array $properties = [])
    {
        parent::__construct(get_object_vars($versionInfo) + $properties);
    }
}
