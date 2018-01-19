<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\User;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;

class UserDeleteData
{
    /** @var ContentInfo|null */
    private $contentInfo;

    /**
     * @param ContentInfo|null $contentInfo
     */
    public function __construct(?ContentInfo $contentInfo = null)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param ContentInfo|null $contentInfo
     */
    public function setContentInfo(?ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }
}
