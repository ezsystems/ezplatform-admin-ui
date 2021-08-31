<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup;

/**
 * @todo Add validation
 */
class ContentTypeGroupsDeleteData
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[]|null */
    protected $contentTypeGroups;

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[]|null $contentTypeGroups
     */
    public function __construct(array $contentTypeGroups = [])
    {
        $this->contentTypeGroups = $contentTypeGroups;
    }

    /**
     * @return array|null
     */
    public function getContentTypeGroups(): ?array
    {
        return $this->contentTypeGroups;
    }

    /**
     * @param array|null $contentTypeGroups
     */
    public function setContentTypeGroups(?array $contentTypeGroups)
    {
        $this->contentTypeGroups = $contentTypeGroups;
    }
}
