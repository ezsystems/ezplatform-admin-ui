<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\RepositoryForms\View;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content as API;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\Repository\Values\Content as Core;
use EzSystems\EzPlatformAdminUi\RepositoryForms\View\ViewParametersListener;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use PHPUnit\Framework\TestCase;

class ViewParametersListenerTest extends TestCase
{
    /** @var PreContentViewEvent */
    private $event;

    public function setUp()
    {
        $contentInfo = $this->generateContentInfo();

        $versionInfo = $this->generateVersionInfo($contentInfo);

        $contentView = new ContentEditView();
        $contentView->setParameters(['content' => $this->generateContent($versionInfo)]);

        $this->event = new PreContentViewEvent($contentView);
    }

    public function testSetViewTemplateParameters()
    {
        $locations = [new Core\Location(), new Core\Location()];

        $contentInfo = $this->generateContentInfo();

        $versionInfo = $this->generateVersionInfo($contentInfo);
        $content = $this->generateContent($versionInfo);
        $location = $this->generateLocation();

        $contentView = new ContentEditView();
        $contentView->setParameters([
            'content' => $content,
            'location' => $location,
        ]);

        $event = new PreContentViewEvent($contentView);

        $locationService = $this->createMock(LocationService::class);
        $locationService
            ->method('loadParentLocationsForDraftContent')
            ->with($versionInfo)
            ->willReturn($locations);

        $viewParametersListener = new ViewParametersListener($locationService);
        $viewParametersListener->setViewTemplateParameters($event);

        $this->assertSame($locations, $contentView->getParameter('parentLocations'));
    }

    /**
     * @param int|null $parentLocationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function generateLocation(int $parentLocationId = null): API\Location
    {
        return new Core\Location(['parentLocationId' => $parentLocationId]);
    }

    public function testSetViewTemplateParametersWithMainLocationId()
    {
        $mainLocationId = 123;
        $parentLocationId = 456;
        $published = true;

        $parentLocations = [new Core\Location(['id' => $parentLocationId])];
        $contentInfo = $this->generateContentInfo($mainLocationId, $published);
        $versionInfo = $this->generateVersionInfo($contentInfo);
        $content = $this->generateContent($versionInfo);
        $location = $this->generateLocation($parentLocationId);

        $contentView = new ContentEditView();
        $contentView->setParameters([
            'content' => $content,
            'location' => $location,
            'parentLocations' => [],
        ]);

        $event = new PreContentViewEvent($contentView);

        $locationService = $this->createMock(LocationService::class);
        $locationService
            ->method('loadParentLocationsForDraftContent')
            ->with($versionInfo)
            ->willReturn($parentLocations);
        $locationService
            ->method('loadLocation')
            ->with($parentLocationId)
            ->willReturn(reset($parentLocations));

        $viewParametersListener = new ViewParametersListener($locationService);
        $viewParametersListener->setViewTemplateParameters($event);

        $this->assertSame([], $contentView->getParameter('parentLocations'));
        $this->assertSame(reset($parentLocations), $contentView->getParameter('parentLocation'));
    }

    public function testSetViewTemplateParametersWithoutContentEditViewInstance()
    {
        $contentView = $this->createMock(View::class);

        $locationService = $this->createMock(LocationService::class);
        $locationService->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $viewParametersListener = new ViewParametersListener($locationService);

        $this->assertNull($viewParametersListener->setViewTemplateParameters(new PreContentViewEvent($contentView)));
    }

    public function testSubscribedEvents()
    {
        $locationService = $this->createMock(LocationService::class);
        $locationService->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $viewParametersListener = new ViewParametersListener($locationService);

        $this->assertSame([MVCEvents::PRE_CONTENT_VIEW => 'setViewTemplateParameters'],
            $viewParametersListener::getSubscribedEvents());
    }

    /**
     * @param int $mainLocationId
     * @param bool $published
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    private function generateContentInfo(int $mainLocationId = null, bool $published = false): API\ContentInfo
    {
        return new API\ContentInfo(['mainLocationId' => $mainLocationId, 'published' => $published]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo
     */
    private function generateVersionInfo(API\ContentInfo $contentInfo): API\VersionInfo
    {
        return new Core\VersionInfo(['contentInfo' => $contentInfo]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    private function generateContent(API\VersionInfo $versionInfo): API\Content
    {
        return new Core\Content(['versionInfo' => $versionInfo]);
    }
}
