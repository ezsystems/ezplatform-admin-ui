<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Service;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Service\PermissionAwareLocationResolver;
use PHPUnit\Framework\TestCase;
use eZ\Publish\API\Repository\LocationService;

final class PermissionAwareLocationResolverTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \EzSystems\EzPlatformAdminUi\Service\LocationResolver */
    private $locationResolver;

    public function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);

        $this->locationResolver = new PermissionAwareLocationResolver($this->locationService);
    }

    /**
     * Test for the resolveLocation() method.
     *
     * @covers \EzSystems\EzPlatformAdminUi\Service\PermissionAwareLocationResolver::resolveLocation()
     */
    public function testResolveMainLocation(): void
    {
        $contentInfo = new ContentInfo(['mainLocationId' => 42]);
        $location = new Location(['id' => 42]);

        // User has access to the main Location
        $this->locationService
            ->method('loadLocation')
            ->willReturn($location);

        $this->assertSame($location, $this->locationResolver->resolveLocation($contentInfo));
    }

    /**
     * Test for the resolveLocation() method.
     *
     * @covers \EzSystems\EzPlatformAdminUi\Service\PermissionAwareLocationResolver::resolveLocation()
     */
    public function testResolveSecondaryLocation(): void
    {
        $contentInfo = new ContentInfo(['mainLocationId' => 42]);
        $location1 = new Location(['id' => 43]);
        $location2 = new Location(['id' => 44]);

        // User doesn't have access to main location but to the third Content's location
        $this->locationService
            ->method('loadLocation')
            ->willThrowException($this->createMock(UnauthorizedException::class));

        $this->locationService
            ->method('loadLocations')
            ->willReturn([$location1, $location2]);

        $this->assertSame($location1, $this->locationResolver->resolveLocation($contentInfo));
    }
}
