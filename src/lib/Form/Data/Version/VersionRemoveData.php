<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Data\Version;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;

/**
 * @todo Add validation
 */
class VersionRemoveData
{
    /** @var ContentInfo|null */
    protected $contentInfo;

    /** @var array|null */
    protected $versions;

    /**
     * @param ContentInfo|null $contentInfo
     * @param array|null $versions
     */
    public function __construct(?ContentInfo $contentInfo = null, array $versions = [])
    {
        $this->contentInfo = $contentInfo;
        $this->versions = $versions;
    }

    /**
     * @return ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param ContentInfo|null $contentInfo
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
