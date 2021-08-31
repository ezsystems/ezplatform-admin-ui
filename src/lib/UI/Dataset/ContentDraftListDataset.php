<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\DraftList\ContentDraftListItemInterface;
use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class ContentDraftListDataset
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraftInterface[] */
    private $data = [];

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        ValueFactory $valueFactory
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User|null $user
     * @param int $offset
     * @param int $limit
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\ContentDraftListDataset
     */
    public function load(User $user = null, int $offset = 0, int $limit = 10): self
    {
        $contentDraftListItems = $this->contentService->loadContentDraftList($user, $offset, $limit)->items;

        $contentTypes = $contentTypeIds = [];
        foreach ($contentDraftListItems as $contentDraftListItem) {
            if ($contentDraftListItem->hasVersionInfo()) {
                $contentTypeIds[] = $contentDraftListItem->getVersionInfo()->getContentInfo()->contentTypeId;
            }
        }

        if (!empty($contentTypeIds)) {
            $contentTypes = $this->contentTypeService->loadContentTypeList(array_unique($contentTypeIds));
        }

        $this->data = array_map(
            function (ContentDraftListItemInterface $contentDraftListItem) use ($contentTypes) {
                if ($contentDraftListItem->hasVersionInfo()) {
                    $versionInfo = $contentDraftListItem->getVersionInfo();
                    $contentType = $contentTypes[$versionInfo->getContentInfo()->contentTypeId];

                    return $this->valueFactory->createContentDraft($contentDraftListItem, $contentType);
                }

                return $this->valueFactory->createUnauthorizedContentDraft($contentDraftListItem);
            },
            $contentDraftListItems
        );

        return $this;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraftInterface[]
     */
    public function getContentDrafts(): array
    {
        return $this->data;
    }
}
