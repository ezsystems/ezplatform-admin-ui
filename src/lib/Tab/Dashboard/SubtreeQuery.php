<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;


use eZ\Publish\API\Repository\Values\Content\Query;

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
