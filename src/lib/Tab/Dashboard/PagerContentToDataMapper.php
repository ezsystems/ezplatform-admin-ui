<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use Pagerfanta\Pagerfanta;

class PagerContentToDataMapper
{
    /** @var ContentService */
    protected $contentService;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var UserService */
    protected $userService;

    /**
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     * @param UserService $userService
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        UserService $userService
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
    }

    /**
     * @param Pagerfanta $pager
     *
     * @return array
     */
    public function map(Pagerfanta $pager): array
    {
        $data = [];

        foreach ($pager as $content) {
            $contentInfo = $this->contentService->loadContentInfo($content->id);

            try {
                $contributor = $this->userService->loadUser($contentInfo->ownerId);
            } catch (NotFoundException $e) {
                $contributor = null;
            }

            $data[] = [
                'contentId' => $content->id,
                'name' => $contentInfo->name,
                'language' => $contentInfo->mainLanguageCode,
                'contributor' => $contributor,
                'version' => $content->versionInfo->versionNo,
                'type' => $this->contentTypeService->loadContentType($contentInfo->contentTypeId)->getName(),
                'modified' => $content->versionInfo->modificationDate,
                'initialLanguageCode' => $content->versionInfo->initialLanguageCode,
            ];
        }

        return $data;
    }
}
