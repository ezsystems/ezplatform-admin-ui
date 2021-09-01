<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\AdminUi\REST\Security;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Ibexa\AdminUi\REST\Security\NonAdminRESTRequestMatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class NonAdminRESTRequestMatcherTest extends TestCase
{
    public function testMatchRESTRequestInAdminContext(): void
    {
        $siteAccessMock = $this->createMock(SiteAccess::class);
        $siteAccessMock->name = 'admin';
        $adminRESTRequestMatcher = new NonAdminRESTRequestMatcher(
            [
                'admin_group' => [
                    'admin',
                ],
            ]
        );

        $request = $this->createMock(Request::class);
        $request->attributes = $this->createMock(ParameterBag::class);

        $request->attributes
            ->expects(self::at(0))
            ->method('get')
            ->with('is_rest_request')
            ->willReturn(true);

        $request->attributes
            ->expects(self::at(1))
            ->method('get')
            ->with('siteaccess')
            ->willReturn($siteAccessMock);

        self::assertFalse($adminRESTRequestMatcher->matches($request));
    }

    public function testMatchNonRESTRequest(): void
    {
        $adminRESTRequestMatcher = new NonAdminRESTRequestMatcher([]);

        $request = $this->createMock(Request::class);
        $request->attributes = $this->createMock(ParameterBag::class);

        $request->attributes
            ->expects(self::at(0))
            ->method('get')
            ->with('is_rest_request')
            ->willReturn(false);

        self::assertFalse($adminRESTRequestMatcher->matches($request));
    }

    public function testMatchRESTRequestNotInAdminContext(): void
    {
        $siteAccessMock = $this->createMock(SiteAccess::class);
        $siteAccessMock->name = 'admin';
        $nonAdminSiteAccessMock = $this->createMock(SiteAccess::class);
        $nonAdminSiteAccessMock->name = 'ibexa';
        $adminRESTRequestMatcher = new NonAdminRESTRequestMatcher(
            [
                'admin_group' => [
                    'admin',
                ],
                'another_group' => [
                    'ibexa',
                ],
            ]
        );

        $request = $this->createMock(Request::class);
        $request->attributes = $this->createMock(ParameterBag::class);

        $request->attributes
            ->expects(self::at(0))
            ->method('get')
            ->with('is_rest_request')
            ->willReturn(true);

        $request->attributes
            ->expects(self::at(1))
            ->method('get')
            ->with('siteaccess')
            ->willReturn($nonAdminSiteAccessMock);

        self::assertTrue($adminRESTRequestMatcher->matches($request));
    }
}

class_alias(NonAdminRESTRequestMatcherTest::class, 'EzSystems\EzPlatformAdminUi\Tests\REST\Security\NonAdminRESTRequestMatcherTest');
