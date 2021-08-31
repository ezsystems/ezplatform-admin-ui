<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Version;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;

/**
 * @todo Add validation
 */
class VersionRemoveData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo|null */
    protected $contentInfo;

    /** @var array|null */
    protected $versions;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param array|null $versions
     */
    public function __construct(?ContentInfo $contentInfo = null, array $versions = [])
    {
        $this->contentInfo = $contentInfo;
        $this->versions = $versions;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     */
    public function setContentInfo(?ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return array|null
     */
    public function getVersions(): ?array
    {
        return $this->versions;
    }

    /**
     * @param array|null $versions
     */
    public function setVersions(?array $versions)
    {
        $this->versions = $versions;
    }
}
