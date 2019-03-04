<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;

class ContentVisibilityUpdateData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo|null */
    private $contentInfo;

    /** @var bool|null */
    private $visible;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param bool|null $visible
     */
    public function __construct(
        ContentInfo $contentInfo = null,
        bool $visible = null
    ) {
        $this->contentInfo = $contentInfo;
        $this->visible = $visible;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @return bool|null
     */
    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     */
    public function setContentInfo(ContentInfo $contentInfo): void
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @param bool $visible
     */
    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }
}
