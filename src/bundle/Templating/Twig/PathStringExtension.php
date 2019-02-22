<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use eZ\Publish\API\Repository\LocationService;
use Twig_Extension;
use Twig_SimpleFunction;

class PathStringExtension extends Twig_Extension
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
            new Twig_SimpleFunction(
                'ez_path_string_to_locations',
                [$this, 'getLocationList']
            ),
        ];
    }

    /**
     * @param string $pathString
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getLocationList(string $pathString): array
    {
        $pathStringParts = explode('/', trim($pathString, '/'));
        array_shift($pathStringParts);

        $locationList = [];
        foreach ($pathStringParts as $locationId) {
            $locationList[] = $this->locationService->loadLocation($locationId);
        }

        return $locationList;
    }
}
