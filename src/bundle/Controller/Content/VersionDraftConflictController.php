<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\Content;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentDraftHasConflict;
use EzSystems\EzPlatformAdminUi\Specification\ContentIsUser;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VersionDraftConflictController extends Controller
{
    /** @var LocationService */
    private $locationService;

    /** @var ContentService */
    private $contentService;

    /** @var DatasetFactory */
    private $datasetFactory;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\API\Repository\UserService $userService
     */
    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        DatasetFactory $datasetFactory,
        UserService $userService
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->datasetFactory = $datasetFactory;
        $this->userService = $userService;
    }

    /**
     * @param int $contentId
     * @param int|null $locationId
     *
     * @return Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\BadStateException
     */
    public function draftHasNoConflictAction(int $contentId, ?int $locationId = null): Response
    {
        $content = $this->contentService->loadContent($contentId);
        $location = $this->locationService->loadLocation(
            $locationId ?? $content->contentInfo->mainLocationId
        );
        $contentInfo = $content->contentInfo;

        if ((new ContentDraftHasConflict($this->contentService))->isSatisfiedBy($contentInfo)) {
            $versionsDataset = $this->datasetFactory->versions();
            $versionsDataset->load($contentInfo);
            $conflictedDrafts = $versionsDataset->getConflictedDraftVersions($contentInfo->currentVersionNo);

            $modalContent = $this->renderView('@ezdesign/content/modal_draft_conflict.html.twig', [
                'conflicted_drafts' => $conflictedDrafts,
                'location' => $location,
                'is_content_user' => (new ContentIsUser($this->userService))->isSatisfiedBy($content),
            ]);

            return new Response($modalContent, Response::HTTP_CONFLICT);
        }

        return new Response();
    }
}
