<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Version;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;

class VersionHasConflict extends AbstractSpecification
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var string */
    private $languageCode;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param string $languageCode
     */
    public function __construct(ContentService $contentService, string $languageCode)
    {
        $this->contentService = $contentService;
        $this->languageCode = $languageCode;
    }

    /**
     * Checks if $content has version conflict.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function isSatisfiedBy($versionInfo): bool
    {
        $versions = $this->contentService->loadVersions($versionInfo->getContentInfo());

        foreach ($versions as $checkedVersionInfo) {
            if ($checkedVersionInfo->versionNo > $versionInfo->versionNo
                && $checkedVersionInfo->isPublished()
                && $checkedVersionInfo->initialLanguageCode === $this->languageCode
            ) {
                return true;
            }
        }

        return false;
    }
}
