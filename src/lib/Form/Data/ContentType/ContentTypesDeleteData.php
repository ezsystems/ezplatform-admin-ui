<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;

/**
 * @todo Add validation
 */
class ContentTypesDeleteData
{
    /** @var ContentType[]|null */
    protected $contentTypes;

    /**
     * @param ContentType[]|null $contentTypes
     */
    public function __construct(array $contentTypes = [])
    {
        $this->contentTypes = $contentTypes;
    }

    /**
     * @return array|null
     */
    public function getContentTypes(): ?array
    {
        return $this->contentTypes;
    }

    /**
     * @param ContentType[]|null $contentTypes
     */
    public function setContentTypes(?array $contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }
}
