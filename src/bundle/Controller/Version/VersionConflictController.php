<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\Version;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use EzSystems\EzPlatformAdminUi\Specification\Version\VersionHasConflict;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VersionConflictController extends Controller
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Checks if Version has conflict with another published Version.
     *
     * If Version has no conflict, return empty Response. If it has conflict return HTML with content of modal.
     *
     * @param int $contentId
     * @param int $versionNo
     * @param string $languageCode
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\BadStateException
     */
    public function versionHasNoConflictAction(int $contentId, int $versionNo, string $languageCode): Response
    {
        $versionInfo = $this->contentService->loadVersionInfoById($contentId, $versionNo);

        if (!$versionInfo->isDraft()) {
            throw new BadStateException('Version status', 'the status is not draft');
        }

        if ((new VersionHasConflict($this->contentService, $languageCode))->isSatisfiedBy($versionInfo)) {
            return new Response('', Response::HTTP_CONFLICT);
        }

        return new Response();
    }
}
