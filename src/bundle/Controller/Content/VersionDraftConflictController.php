<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
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
use Symfony\Contracts\Translation\TranslatorInterface;

class VersionDraftConflictController extends Controller
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    private $datasetFactory;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
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
     * @param int|null $locationId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function draftHasNoConflictAction(
        int $contentId,
        string $languageCode,
        ?int $locationId = null
    ): Response {
        $content = $this->contentService->loadContent($contentId);
        $contentInfo = $content->contentInfo;

        try {
            $contentDraftHasConflict = (new ContentDraftHasConflict($this->contentService, $languageCode))->isSatisfiedBy($contentInfo);
        } catch (UnauthorizedException $e) {
            $error = $this->translator->trans(
                /** @Desc("Cannot check if the draft has conflicts with other drafts. %error%.") */
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
            $locationId = $locationId ?? $contentInfo->mainLocationId;
            try {
                $location = $this->locationService->loadLocation($locationId);
            } catch (UnauthorizedException $e) {
                // Will return list of locations user has *read* access to, or empty array if none
                $availableLocations = $this->locationService->loadLocations($contentInfo);
                // will return null if array of availableLocations is empty
                $location = array_shift($availableLocations);
            }

            $modalContent = $this->renderView('@ezdesign/content/modal/draft_conflict.html.twig', [
                'conflicted_drafts' => $conflictedDrafts,
                'location' => $location,
                'content_is_user' => (new ContentIsUser($this->userService))->isSatisfiedBy($content),
            ]);

            return new Response($modalContent, Response::HTTP_CONFLICT);
        }

        return new Response();
    }
}
