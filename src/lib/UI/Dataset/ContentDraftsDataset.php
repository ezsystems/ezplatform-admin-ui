<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\EzPlatformAdminUi\UI\Value\Content\VersionId;

class ContentDraftsDataset
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var array */
    private $data = [];

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService)
    {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User|null $user
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\ContentDraftsDataset
     */
    public function load(User $user = null): self
    {
        try {
            $contentDrafts = $this->contentService->loadContentDrafts($user);
        } catch (UnauthorizedException $e) {
            // if user has no access content/versionread for one of versions, exception is caught and draft array is empty
            $contentDrafts = [];
        }

        $contentDrafts = array_filter($contentDrafts, function (VersionInfo $version) {
            $contentInfo = $version->getContentInfo();

            if (null === $contentInfo->mainLocationId) {
                $locations = $this->locationService->loadParentLocationsForDraftContent($version);
                // empty locations here means Location has been trashed and Draft should be ignored
                return !empty($locations);
            }

            return true;
        });

        // ContentService::loadContentDrafts returns unsorted list of VersionInfo.
        // Sort results by modification date, descending.
        usort($contentDrafts, function (VersionInfo $a, VersionInfo $b) {
            return $b->modificationDate <=> $a->modificationDate;
        });

        $this->data = array_map(
            function (VersionInfo $versionInfo) {
                return $this->mapContentDraft($versionInfo);
            },
            $contentDrafts
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getContentDrafts(): array
    {
        return $this->data;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $draft
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function mapContentDraft(VersionInfo $draft): array
    {
        $contentInfo = $draft->getContentInfo();
        $contentType = $this->contentTypeService->loadContentType(
            $contentInfo->contentTypeId
        );

        return [
            'id' => new VersionId(
                $contentInfo->id,
                $draft->versionNo
            ),
            'contentId' => $contentInfo->id,
            'name' => $draft->getName(),
            'type' => $contentType->getName(),
            'language' => $draft->initialLanguageCode,
            'version' => $draft->versionNo,
            'modified' => $draft->modificationDate,
        ];
    }
}
