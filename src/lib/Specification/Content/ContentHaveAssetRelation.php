<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Content;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Relation;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

class ContentHaveAssetRelation extends AbstractSpecification
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param $item
     *
     * @return bool
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof Content) {
            throw new InvalidArgumentException($item, sprintf('Must be instance of %s', Content::class));
        }

        $relations = $this->contentService->loadRelations($item->versionInfo);

        foreach ($relations as $relation) {
            if ($relation->type === Relation::ASSET) {
                return true;
            }
        }

        return false;
    }
}
