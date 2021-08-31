<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query;
use EzSystems\EzPlatformAdminUi\UniversalDiscovery\Provider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UniversalDiscoveryController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\UniversalDiscovery\Provider */
    private $provider;

    public function __construct(
        Provider $provider
    ) {
        $this->provider = $provider;
    }

    public function locationsAction(Request $request)
    {
        return new JsonResponse(
            $this->provider->getLocations(
                explode(
                    ',',
                    $request->query->get('locationIds', '')
                )
            )
        );
    }

    public function locationAction(
        Request $request,
        int $locationId
    ): JsonResponse {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 25);
        $sortClauseName = $request->query->getAlpha('sortClause', Provider::SORT_CLAUSE_DATE_PUBLISHED);
        $sortOrder = $request->query->getAlpha('sortOrder', Query::SORT_ASC);

        $sortClause = $this->provider->getSortClause($sortClauseName, $sortOrder);

        return new JsonResponse(
            $this->provider->getLocationData($locationId, $offset, $limit, $sortClause)
        );
    }

    public function locationGridViewAction(
        Request $request,
        int $locationId
    ): JsonResponse {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 25);
        $sortClauseName = $request->query->getAlpha('sortClause', Provider::SORT_CLAUSE_DATE_PUBLISHED);
        $sortOrder = $request->query->getAlpha('sortOrder', Query::SORT_ASC);

        $sortClause = $this->provider->getSortClause($sortClauseName, $sortOrder);

        return new JsonResponse(
            $this->provider->getLocationGridViewData($locationId, $offset, $limit, $sortClause)
        );
    }

    public function accordionAction(
        Request $request,
        int $locationId
    ): JsonResponse {
        $limit = $request->query->getInt('limit', 25);
        $sortClauseName = $request->query->getAlpha('sortClause', Provider::SORT_CLAUSE_DATE_PUBLISHED);
        $sortOrder = $request->query->getAlpha('sortOrder', Query::SORT_ASC);
        $rootLocationId = $request->query->getInt('rootLocationId', Provider::ROOT_LOCATION_ID);

        $sortClause = $this->provider->getSortClause($sortClauseName, $sortOrder);
        $breadcrumbLocations = $this->provider->getBreadcrumbLocations($locationId);

        $columns = $this->provider->getColumns($locationId, $limit, $sortClause, false, $rootLocationId);

        return new JsonResponse([
            'breadcrumb' => array_map(
                function (Location $location) {
                    return $this->provider->getRestFormat($location);
                },
                $breadcrumbLocations
            ),
            'columns' => $columns,
        ]);
    }

    public function accordionGridViewAction(
        Request $request,
        int $locationId
    ): JsonResponse {
        $limit = $request->query->getInt('limit', 25);
        $sortClauseName = $request->query->getAlpha('sortClause', Provider::SORT_CLAUSE_DATE_PUBLISHED);
        $sortOrder = $request->query->getAlpha('sortOrder', Query::SORT_ASC);
        $rootLocationId = $request->query->getInt('rootLocationId', Provider::ROOT_LOCATION_ID);

        $sortClause = $this->provider->getSortClause($sortClauseName, $sortOrder);

        $columns = $this->provider->getColumns($locationId, $limit, $sortClause, true, $rootLocationId);

        return new JsonResponse([
            'breadcrumb' => array_map(
                function (Location $location) {
                    return $this->provider->getRestFormat($location);
                },
                $this->provider->getBreadcrumbLocations($locationId)
            ),
            'columns' => $columns,
        ]);
    }
}
