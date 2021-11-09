<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\Repository\LocationResolver\LocationResolver;
use EzSystems\EzPlatformAdminUi\Pagination\Mapper\AbstractPagerContentToDataMapper;
use Pagerfanta\Pagerfanta;

class PagerContentToDataMapper extends AbstractPagerContentToDataMapper
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\UserService */
    protected $userService;

    /** @var \eZ\Publish\Core\Repository\LocationResolver\LocationResolver */
    protected $locationResolver;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\Core\Repository\LocationResolver\LocationResolver $locationResolver
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        UserService $userService,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        TranslationHelper $translationHelper,
        LanguageService $languageService,
        LocationResolver $locationResolver
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->locationResolver = $locationResolver;

        parent::__construct(
            $contentTypeService,
            $userService,
            $userLanguagePreferenceProvider,
            $translationHelper,
            $languageService
        );
    }

    /**
     * @param \Pagerfanta\Pagerfanta $pager
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\ForbiddenException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function map(Pagerfanta $pager): array
    {
        $data = [];
        $contentTypeIds = [];

        foreach ($pager as $content) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
            $contentInfo = $content->contentInfo;

            $contentTypeIds[] = $contentInfo->contentTypeId;
            $data[] = [
                'content' => $content,
                'contentTypeId' => $contentInfo->contentTypeId,
                'contentId' => $content->id,
                'name' => $this->translationHelper->getTranslatedContentName($content),
                'language' => $contentInfo->mainLanguageCode,
                'contributor' => $this->getVersionContributor($content->versionInfo),
                'version' => $content->versionInfo->versionNo,
                'content_type' => $content->getContentType(),
                'modified' => $content->versionInfo->modificationDate,
                'initialLanguageCode' => $content->versionInfo->initialLanguageCode,
                'content_is_user' => $this->isContentIsUser($content),
                'available_enabled_translations' => $this->getAvailableTranslations($content, true),
                'resolvedLocation' => $this->locationResolver->resolveLocation($contentInfo),
            ];
        }

        $this->setTranslatedContentTypesNames($data, $contentTypeIds);

        return $data;
    }
}
