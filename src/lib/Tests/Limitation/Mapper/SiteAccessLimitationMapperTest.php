<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation\SiteAccessLimitation;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\SiteAccessLimitationMapper;
use EzSystems\EzPlatformAdminUi\Siteaccess\SiteAccessKeyGenerator;
use EzSystems\EzPlatformAdminUi\Siteaccess\SiteAccessKeyGeneratorInterface;
use PHPUnit\Framework\TestCase;

class SiteAccessLimitationMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $siteAccessList = [
            '2356372769' => 'foo',
            '1996459178' => 'bar',
            '2015626392' => 'baz',
        ];

        $siteAccesses = array_map(
            static function (string $siteAccessName): SiteAccess {
                return new SiteAccess($siteAccessName);
            },
            $siteAccessList
        );

        $limitation = new SiteAccessLimitation(
            [
                'limitationValues' => array_keys($siteAccessList),
            ]
        );

        $siteAccessesGeneratorInterface = $this->createMock(SiteAccessKeyGeneratorInterface::class);
        $siteAccessesGeneratorInterface
            ->method('generate')
            ->willReturn(new SiteAccessKeyGenerator());

        $siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $siteAccessService->method('getAll')->willReturn($siteAccesses);

        $mapper = new SiteAccessLimitationMapper($siteAccessService, $siteAccessesGeneratorInterface);
        $result = $mapper->mapLimitationValue($limitation);

        $this->assertEquals(array_values($siteAccessList), $result);
    }
}
