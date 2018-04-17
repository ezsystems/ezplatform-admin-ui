<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Location;

use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;

class IsContainer extends AbstractSpecification
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct($contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $item
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function isSatisfiedBy($item): bool
    {
        return $this->contentTypeService->loadContentType(
            $item->getContentInfo()->contentTypeId
        )->isContainer;
    }
}
