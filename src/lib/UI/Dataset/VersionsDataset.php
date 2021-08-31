<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class VersionsDataset
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    protected $valueFactory;

    /** @var \eZ\Publish\API\Repository\Values\Content\VersionInfo[] */
    protected $data;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(ContentService $contentService, ValueFactory $valueFactory)
    {
        $this->contentService = $contentService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return VersionsDataset
     */
    public function load(ContentInfo $contentInfo): self
    {
        $this->data = array_map(
            [$this->valueFactory, 'createVersionInfo'],
            $this->contentService->loadVersions($contentInfo)
        );

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo[]
     */
    public function getVersions(): array
    {
        return $this->data;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo[]
     */
    public function getDraftVersions(): array
    {
        return $this->filterVersions(
            $this->data,
            static function (VersionInfo $versionInfo) {
                return $versionInfo->isDraft();
            }
        );
    }

    /**
     * @param int $currentVersionNo
     * @param string $languageCode
     *
     * @return array
     */
    public function getConflictedDraftVersions(int $currentVersionNo, string $languageCode): array
    {
        return $this->filterVersions(
            $this->data,
            static function (VersionInfo $versionInfo) use ($currentVersionNo, $languageCode) {
                return $versionInfo->isDraft()
                    && $versionInfo->versionNo > $currentVersionNo
                    && $versionInfo->initialLanguageCode === $languageCode;
            }
        );
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo[]
     */
    public function getPublishedVersions(): array
    {
        return $this->filterVersions(
            $this->data,
            static function (VersionInfo $versionInfo) {
                return $versionInfo->isPublished();
            }
        );
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo[]
     */
    public function getArchivedVersions(): array
    {
        return $this->filterVersions(
            $this->data,
            static function (VersionInfo $versionInfo) {
                return $versionInfo->isArchived();
            }
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo[] $versions
     * @param callable $callable
     *
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo[]
     */
    protected function filterVersions(array $versions, callable $callable): array
    {
        return array_values(array_filter($versions, $callable));
    }
}
