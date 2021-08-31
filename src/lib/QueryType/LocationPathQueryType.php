<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\QueryType;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\QueryType\OptionsResolverBasedQueryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LocationPathQueryType extends OptionsResolverBasedQueryType
{
    public static function getName(): string
    {
        return 'EzPlatformAdminUi:LocationPath';
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setDefined('rootLocationId')
            ->setRequired('location')
            ->setAllowedTypes('location', Location::class)
            ->setAllowedTypes('rootLocationId', ['int', 'null'])
        ;
    }

    protected function doGetQuery(array $parameters): Query
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $parameters['location'];
        /** @var int $rootLocationId */
        $rootLocationId = $parameters['rootLocationId'];

        $filter = $location->id === $rootLocationId
            ? new Query\Criterion\ParentLocationId($rootLocationId)
            : new Query\Criterion\LocationId($this->getParentLocationPath($location));

        return new LocationQuery(['filter' => $filter]);
    }

    private function getParentLocationPath(Location $location): array
    {
        $parentPath = array_slice($location->path, 0, -1);

        return array_map('intval', $parentPath);
    }
}
