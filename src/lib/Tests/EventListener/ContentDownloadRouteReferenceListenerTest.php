<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\RouteReferenceGenerationEvent;
use eZ\Publish\Core\MVC\Symfony\Routing\RouteReference;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\EventListener\ContentDownloadRouteReferenceListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ContentDownloadRouteReferenceListenerTest extends TestCase
{
    private const EXAMPLE_SITEACCESS_GROUPS = [
        'admin_group' => [
            'admin',
        ],
        'site_group' => [
            'non-admin',
        ],
    ];

    public function testOnRouteReferenceGenerationSkipNonSupportedRoutes(): void
    {
        $expectedRouteReference = new RouteReference(
            'non_supported_route',
            [
                'foo' => 'foo',
                'bar' => 'bar',
                'baz' => 'baz',
            ]
        );

        $event = new RouteReferenceGenerationEvent(
            clone $expectedRouteReference,
            $this->createMock(Request::class)
        );

        $listener = new ContentDownloadRouteReferenceListener(self::EXAMPLE_SITEACCESS_GROUPS);
        $listener->onRouteReferenceGeneration($event);

        $this->assertEquals($expectedRouteReference, $event->getRouteReference());
    }

    public function testOnRouteReferenceGenerationSkipNonAdminSiteAccesses(): void
    {
        $expectedRouteReference = new RouteReference(
            ContentDownloadRouteReferenceListener::CONTENT_DOWNLOAD_ROUTE_NAME,
            [
                'foo' => 'foo',
                'bar' => 'bar',
                'baz' => 'baz',
            ]
        );

        $request = new Request();
        $request->attributes->set('siteaccess', new SiteAccess('non-admin'));

        $event = new RouteReferenceGenerationEvent(clone $expectedRouteReference, $request);

        $listener = new ContentDownloadRouteReferenceListener(self::EXAMPLE_SITEACCESS_GROUPS);
        $listener->onRouteReferenceGeneration($event);

        $this->assertEquals($expectedRouteReference, $event->getRouteReference());
    }

    public function testOnRouteReferenceGenerationForcesAdminSiteAccess(): void
    {
        $request = new Request();
        $request->attributes->set('siteaccess', new SiteAccess('admin'));

        $event = new RouteReferenceGenerationEvent(
            new RouteReference(
                ContentDownloadRouteReferenceListener::CONTENT_DOWNLOAD_ROUTE_NAME,
                [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'baz' => 'baz',
                ]
            ),
            $request
        );

        $listener = new ContentDownloadRouteReferenceListener(self::EXAMPLE_SITEACCESS_GROUPS);
        $listener->onRouteReferenceGeneration($event);

        $this->assertEquals(
            new RouteReference(
                ContentDownloadRouteReferenceListener::CONTENT_DOWNLOAD_ROUTE_NAME,
                [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'baz' => 'baz',
                    'siteaccess' => 'admin',
                ]
            ),
            $event->getRouteReference()
        );
    }
}
