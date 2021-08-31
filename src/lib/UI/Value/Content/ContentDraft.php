<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\Content;

use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;

class ContentDraft implements ContentDraftInterface
{
    /** @var \eZ\Publish\API\Repository\Values\Content\VersionInfo */
    private $versionInfo;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\VersionId */
    private $versionId;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    private $contentType;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\Content\VersionId $versionId
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     */
    public function __construct(
        VersionInfo $versionInfo,
        VersionId $versionId,
        ContentType $contentType
    ) {
        $this->versionInfo = $versionInfo;
        $this->versionId = $versionId;
        $this->contentType = $contentType;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo
     */
    public function getVersionInfo(): VersionInfo
    {
        return $this->versionInfo;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\VersionId
     */
    public function getVersionId(): VersionId
    {
        return $this->versionId;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @return bool
     */
    public function isAccessible(): bool
    {
        return true;
    }
}
