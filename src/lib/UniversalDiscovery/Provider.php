<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query;

interface Provider
{
    public const ROOT_LOCATION_ID = 1;

    public const SORT_CLAUSE_DATE_PUBLISHED = 'DatePublished';
    public const SORT_CLAUSE_CONTENT_NAME = 'ContentName';

    public function getColumns(
        int $locationId,
        int $limit,
        Query\SortClause $sortClause,
        bool $gridView = false,
        int $rootLocationId = self::ROOT_LOCATION_ID
    ): array;

    public function getBreadcrumbLocations(
        int $locationId,
        int $rootLocationId = self::ROOT_LOCATION_ID
    ): array;

    public function getLocationPermissionRestrictions(Location $location): array;

    public function getSubitemContents(
        int $locationId,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array;

    public function getSubitemLocations(
        int $locationId,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array;

    public function getLocationData(
        int $locationId,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array;

    public function getLocationGridViewData(
        int $locationId,
        int $offset,
        int $limit,
        Query\SortClause $sortClause
    ): array;

    public function getLocations(array $locationIds): array;

    public function getRestFormat($valueObject): array;

    public function getSortClause(string $sortClauseName, string $sortOrder): Query\SortClause;
}
