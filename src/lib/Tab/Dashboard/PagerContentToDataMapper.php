<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUi\Specification\ContentIsUser;
use EzSystems\EzPlatformAdminUi\Specification\UserExists;
use Pagerfanta\Pagerfanta;

class PagerContentToDataMapper
{
    /** @var ContentService */
    protected $contentService;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var UserService */
    protected $userService;

    /** @var UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \eZ\Publish\Core\Helper\TranslationHelper */
    private $translationHelper;

    /**
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     * @param UserService $userService
     * @param UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        UserService $userService,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        TranslationHelper $translationHelper
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->translationHelper = $translationHelper;
    }

    /**
     * @param Pagerfanta $pager
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function map(Pagerfanta $pager): array
    {
        $data = [];
        $contentTypeIds = [];

        foreach ($pager as $content) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
            $contentInfo = $content->getVersionInfo()->getContentInfo();

            $contributor = (new UserExists($this->userService))->isSatisfiedBy($contentInfo->ownerId)
                ? $this->userService->loadUser($contentInfo->ownerId) : null;

            $contentTypeIds[] = $contentInfo->contentTypeId;
            $data[] = [
                'content' => $content,
                'contentTypeId' => $contentInfo->contentTypeId,
                'contentId' => $content->id,
                'name' => $this->translationHelper->getTranslatedContentName($content),
                'language' => $contentInfo->mainLanguageCode,
                'contributor' => $contributor,
                'version' => $content->versionInfo->versionNo,
                'content_type' => $content->getContentType(),
                'modified' => $content->versionInfo->modificationDate,
                'initialLanguageCode' => $content->versionInfo->initialLanguageCode,
                'content_is_user' => (new ContentIsUser($this->userService))->isSatisfiedBy($content),
            ];
        }

        // load list of Content Types with proper translated names
        $contentTypes = $this->contentTypeService->loadContentTypeList(
            array_unique($contentTypeIds),
            $this->userLanguagePreferenceProvider->getPreferredLanguages()
        );
        foreach ($data as $idx => $item) {
            /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
            // get content type from bulk-loaded list or fallback to lazy loaded one if not present
            $contentType = isset($contentTypes[$item['contentTypeId']])
                ? $contentTypes[$item['contentTypeId']]
                : $item['content']->getContentType();
            $data[$idx]['type'] = $contentType->getName();
            unset($data[$idx]['content'], $data[$idx]['contentTypeId']);
        }

        return $data;
    }
}
