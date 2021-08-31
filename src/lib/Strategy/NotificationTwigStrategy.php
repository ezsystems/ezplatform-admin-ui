<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Strategy;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use EzSystems\EzPlatformAdminUi\Exception\NoValidResultException;

class NotificationTwigStrategy
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var string */
    private $defaultTemplate;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(
        Repository $repository,
        ContentService $contentService
    ) {
        $this->repository = $repository;
        $this->contentService = $contentService;
    }

    /**
     * @param string $defaultTemplate
     */
    public function setDefault(string $defaultTemplate)
    {
        $this->defaultTemplate = $defaultTemplate;
    }

    /**
     * @param mixed $contentId
     *
     * @return string
     *
     * @throws \EzSystems\Notification\Exception\NoValidResultException
     */
    public function decide($contentId): string
    {
        $contentId = (int)$contentId;

        if ($this->isContentPermanentlyDeleted($contentId)) {
            return '@ezdesign/account/notifications/list_item_deleted.html.twig';
        }
        if ($this->isContentTrashed($contentId)) {
            return '@ezdesign/account/notifications/list_item_trashed.html.twig';
        }
        if (!empty($this->defaultTemplate)) {
            return $this->defaultTemplate;
        }

        throw new NoValidResultException();
    }

    private function isContentPermanentlyDeleted($contentId): bool
    {
        // Using sudo in order to be sure that information is valid in case user no longer have access to content i.e when in trash.
        try {
            $this->repository->sudo(
                function () use ($contentId) {
                    return $this->contentService->loadContentInfo($contentId);
                }
            );

            return false;
        } catch (NotFoundException $exception) {
            return true;
        }
    }

    private function isContentTrashed($contentId): bool
    {
        // Using sudo in order to be sure that information is valid in case user no longer have access to content i.e when in trash.
        $contentInfo = $this->repository->sudo(
            function () use ($contentId) {
                return $this->contentService->loadContentInfo($contentId);
            }
        );

        return $contentInfo->isTrashed();
    }
}
