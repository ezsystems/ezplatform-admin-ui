<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\Content;

use eZ\Publish\API\Repository\Values\Content\DraftList\Item\UnauthorizedContentDraftListItem;

class UnauthorizedContentDraft implements ContentDraftInterface
{
    /** @var \eZ\Publish\API\Repository\Values\Content\DraftList\Item\UnauthorizedContentDraftListItem */
    private $unauthorizedContentDraft;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\DraftList\Item\UnauthorizedContentDraftListItem $unauthorizedContentDraft
     */
    public function __construct(UnauthorizedContentDraftListItem $unauthorizedContentDraft)
    {
        $this->unauthorizedContentDraft = $unauthorizedContentDraft;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\DraftList\Item\UnauthorizedContentDraftListItem
     */
    public function getUnauthorizedContentDraft(): UnauthorizedContentDraftListItem
    {
        return $this->unauthorizedContentDraft;
    }

    /**
     * @return bool
     */
    public function isAccessible(): bool
    {
        return false;
    }
}
