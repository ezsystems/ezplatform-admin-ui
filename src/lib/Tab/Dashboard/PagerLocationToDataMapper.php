<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\LocationResolver\LocationResolver;
use Pagerfanta\Pagerfanta;

final class PagerLocationToDataMapper
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\UserService */
    protected $userService;

    /** @var \eZ\Publish\Core\Repository\LocationResolver\LocationResolver */
    protected $locationResolver;

    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        UserService $userService,
        LocationResolver $locationResolver
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->locationResolver = $locationResolver;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\ForbiddenException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function map(Pagerfanta $pager, bool $doMapVersionInfoData = false): array
    {
        $data = [];

        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        foreach ($pager as $location) {
            $contentInfo = $location->contentInfo;
            $versionInfo = $doMapVersionInfoData ? $this->contentService->loadVersionInfo($contentInfo) : null;
            $contentType = $location->getContentInfo()->getContentType();

            $data[] = [
                'contentTypeId' => $contentInfo->contentTypeId,
                'contentId' => $contentInfo->id,
                'name' => $contentInfo->name,
                'type' => $contentType->getName(),
                'language' => $contentInfo->mainLanguageCode,
                'available_enabled_translations' => [],
                'contributor' => $versionInfo !== null ? $this->getVersionContributor($versionInfo) : null,
                'content_type' => $contentType,
                'modified' => $contentInfo->modificationDate,
                'resolvedLocation' => $this->locationResolver->resolveLocation($contentInfo),
            ];
        }

        return $data;
    }

    private function getVersionContributor(VersionInfo $versionInfo): ?User
    {
        try {
            return $this->userService->loadUser($versionInfo->creatorId);
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
