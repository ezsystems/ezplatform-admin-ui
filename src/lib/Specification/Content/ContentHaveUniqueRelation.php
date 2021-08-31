<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\Content;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Relation;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;

class ContentHaveUniqueRelation extends AbstractSpecification
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
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
            throw new InvalidArgumentException($item, sprintf('Must be an instance of %s', Content::class));
        }

        $relations = $this->contentService->loadRelations($item->versionInfo);

        foreach ($relations as $relation) {
            if (Relation::ASSET === $relation->type) {
                $relationsFromAssetSide = $this->contentService->countReverseRelations(
                    $relation->destinationContentInfo
                );

                if ($relationsFromAssetSide > 1) {
                    return false;
                }
            }
        }

        return true;
    }
}
