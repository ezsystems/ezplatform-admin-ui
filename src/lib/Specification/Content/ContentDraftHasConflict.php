<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Content;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;

class ContentDraftHasConflict extends AbstractSpecification
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
     * Checks if Content has draft conflict.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function isSatisfiedBy($contentInfo): bool
    {
        $versions = $this->contentService->loadVersions($contentInfo);

        foreach ($versions as $checkedVersionInfo) {
            if ($checkedVersionInfo->isDraft()
                && $checkedVersionInfo->versionNo > $contentInfo->currentVersionNo
                && $checkedVersionInfo->initialLanguageCode === $this->languageCode
            ) {
                return true;
            }
        }

        return false;
    }
}
