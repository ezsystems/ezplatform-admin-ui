<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;

class ContentTypeGroupUpdateData
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup */
    private $contentTypeGroup;

    /** @var string */
    private $identifier;

    public function __construct(?ContentTypeGroup $contentTypeGroup = null)
    {
        if ($contentTypeGroup instanceof ContentTypeGroup) {
            $this->contentTypeGroup = $contentTypeGroup;
            $this->identifier = $contentTypeGroup->identifier;
        }
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup
     */
    public function getContentTypeGroup(): ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     */
    public function setContentTypeGroup(ContentTypeGroup $contentTypeGroup)
    {
        $this->contentTypeGroup = $contentTypeGroup;
    }
}
