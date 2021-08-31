<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\Content;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Relation as APIRelation;
use eZ\Publish\Core\Repository\Values\Content\Relation as CoreRelation;

/**
 * Extends original value object in order to provide additional fields.
 */
class Relation extends CoreRelation implements RelationInterface
{
    /**
     * Field definition name for the relation.
     * This will either come from destinationContentInfo OR sourceContentInfo depending upon if reverse relation or normal relation.
     *
     * @var string
     */
    protected $relationFieldDefinitionName;

    /**
     * The content type name for the relation.
     * This will either come from destinationContentInfo OR sourceContentInfo depending upon if reverse relation or normal relation.
     *
     * @var string
     */
    protected $relationContentTypeName;

    /**
     * Main location for the relation.
     * This will either come from destinationContentInfo OR sourceContentInfo depending upon if reverse relation or normal relation.
     *
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected $relationLocation;

    /**
     * The name for the relation.
     * This will either come from destinationContentInfo OR sourceContentInfo depending upon if reverse relation or normal relation.
     *
     * @var string
     */
    protected $relationName;

    /**
     * Source location for the relation.
     *
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected $resolvedSourceLocation;

    /**
     * Destination location for the relation.
     *
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected $resolvedDestinationLocation;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Relation $relation
     * @param array $properties
     */
    public function __construct(APIRelation $relation, array $properties = [])
    {
        parent::__construct(get_object_vars($relation) + $properties);
    }

    /**
     * @return bool
     */
    public function isAccessible(): bool
    {
        return true;
    }
}
