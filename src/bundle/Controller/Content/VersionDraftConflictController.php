<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\Content;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentDraftHasConflict;
use EzSystems\EzPlatformAdminUi\Specification\ContentIsUser;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

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

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        DatasetFactory $datasetFactory,
        UserService $userService,
        TranslatorInterface $translator
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->datasetFactory = $datasetFactory;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @param int $contentId
     * @param string $languageCode
     *
     * @return Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function draftHasNoConflictAction(int $contentId, string $languageCode): Response
    {
        $content = $this->contentService->loadContent($contentId);
        $location = $this->locationService->loadLocation($content->contentInfo->mainLocationId);
        $contentInfo = $content->contentInfo;

        try {
            $contentDraftHasConflict = (new ContentDraftHasConflict($this->contentService, $languageCode))->isSatisfiedBy($contentInfo);
        } catch (UnauthorizedException $e) {
            $error = $this->translator->trans(
                /** @Desc("Cannot check if the draft has no conflict with other drafts. %error%. ") */
                'content.draft.conflict.error',
                ['%error%' => $e->getMessage()],
                'content'
            );

            return new Response($error, Response::HTTP_FORBIDDEN);
        }

        if ($contentDraftHasConflict) {
            $versionsDataset = $this->datasetFactory->versions();
            $versionsDataset->load($contentInfo);
            $conflictedDrafts = $versionsDataset->getConflictedDraftVersions($contentInfo->currentVersionNo, $languageCode);

            $modalContent = $this->renderView('@ezdesign/content/modal_draft_conflict.html.twig', [
                'conflicted_drafts' => $conflictedDrafts,
                'location' => $location,
                'content_is_user' => (new ContentIsUser($this->userService))->isSatisfiedBy($content),
            ]);

            return new Response($modalContent, Response::HTTP_CONFLICT);
        }

        return new Response();
    }
}
