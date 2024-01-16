<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\LocationResolver\LocationResolver;
use Pagerfanta\Pagerfanta;

final class PagerLocationToDataMapper
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\Core\Repository\LocationResolver\LocationResolver */
    private $locationResolver;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    public function __construct(
        ContentService $contentService,
        UserService $userService,
        LocationResolver $locationResolver,
        LanguageService $languageService
    ) {
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->locationResolver = $locationResolver;
        $this->languageService = $languageService;
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
            $contentInfo = $location->getContentInfo();
            $versionInfo = $doMapVersionInfoData ? $location->getContent()->getVersionInfo() : null;
            $contentType = $location->getContentInfo()->getContentType();

            $data[] = [
                'contentTypeId' => $contentInfo->contentTypeId,
                'contentId' => $contentInfo->id,
                'name' => $contentInfo->name,
                'type' => $contentType->getName(),
                'language' => $contentInfo->mainLanguageCode,
                'available_enabled_translations' => $versionInfo !== null ? $this->getAvailableTranslations($versionInfo) : [],
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

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language[]
     */
    private function getAvailableTranslations(
        VersionInfo $versionInfo
    ): array {
        $availableTranslationsLanguages = $this->languageService->loadLanguageListByCode(
            $versionInfo->languageCodes
        );

        return array_filter(
            $availableTranslationsLanguages,
            static function (Language $language): bool {
                return $language->enabled;
            }
        );
    }
}
