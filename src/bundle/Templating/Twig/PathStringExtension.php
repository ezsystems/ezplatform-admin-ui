<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use eZ\Publish\API\Repository\LocationService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PathStringExtension extends AbstractExtension
{
    private $locationService;

    public function __construct(
        LocationService $locationService
    ) {
        $this->locationService = $locationService;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_path_to_locations',
                [$this, 'getLocationList']
            ),
        ];
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    public function getLocationList(string $pathString): array
    {
        $locationIds = array_map(
            'intval',
            explode('/', trim($pathString, '/'))
        );
        array_shift($locationIds);

        return $this->locationService->loadLocationList($locationIds);
    }
}
