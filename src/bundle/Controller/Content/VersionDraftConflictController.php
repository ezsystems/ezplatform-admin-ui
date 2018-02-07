<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\Content;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentDraftHasConflict;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VersionDraftConflictController extends Controller
{
    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var DatasetFactory
     */
    private $datasetFactory;

    /**
     * @param ContentService $contentService
     * @param DatasetFactory $datasetFactory
     */
    public function __construct(ContentService $contentService, DatasetFactory $datasetFactory)
    {
        $this->contentService = $contentService;
        $this->datasetFactory = $datasetFactory;
    }

    /**
     * @param int $contentId
     *
     * @return Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\BadStateException
     */
    public function draftHasNoConflictAction(int $contentId): Response
    {
        $contentInfo = $this->contentService->loadContentInfo($contentId);

        if ((new ContentDraftHasConflict($this->contentService))->isSatisfiedBy($contentInfo)) {
            $versionsDataset = $this->datasetFactory->versions();
            $versionsDataset->load($contentInfo);
            $conflictedDrafts = $versionsDataset->getConflictedDraftVersions($contentInfo->currentVersionNo);

            $modalContent = $this->renderView('@EzPlatformAdminUi/content/modal_draft_conflict.html.twig', [
                'conflicted_drafts' => $conflictedDrafts,
            ]);

            return new Response($modalContent, Response::HTTP_CONFLICT);
        }

        return new Response();
    }
}
