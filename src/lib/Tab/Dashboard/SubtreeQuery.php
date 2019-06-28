<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * @deprecated since version 2.5, to be removed in 3.0. Use '\EzSystems\EzPlatformAdminUi\QueryType\SubtreeQueryType' instead.
 */
class SubtreeQuery extends Query
{
    /**
     * @param string $subtree
     * @param int|null $ownerId
     */
    public function __construct(string $subtree, ?int $ownerId = null)
    {
        $subtreeCriterion = new Query\Criterion\Subtree($subtree);
        $filter = null;

        if (null !== $ownerId) {
            $filter = new Query\Criterion\LogicalAnd([
                $subtreeCriterion,
                new Query\Criterion\UserMetadata(
                    Query\Criterion\UserMetadata::OWNER,
                    Query\Criterion\Operator::EQ,
                    $ownerId
                ),
            ]);
        } else {
            $filter = $subtreeCriterion;
        }

        parent::__construct([
            'filter' => $filter,
            'sortClauses' => [new Query\SortClause\DateModified(Query::SORT_DESC)],
        ]);
    }
}
