<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\RepositoryForms\View;

use PHPUnit\Framework\TestCase;
use EzSystems\EzPlatformAdminUi\RepositoryForms\View\ViewParametersListener;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content as ApiContent;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as APIContentInfo;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;

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

    /**
     * Check if parentLocations paramter is.
     */
    public function testSetViewTemplateParameters()
    {
        $locations = [new Location(), new Location()];

        $contentInfo = $this->generateContentInfo();

        $versionInfo = $this->generateVersionInfo($contentInfo);

        $contentView = new ContentEditView();
        $contentView->setParameters(['content' => $this->generateContent($versionInfo)]);

        $event = new PreContentViewEvent($contentView);

        $locationService = $this->createMock(LocationService::class);
        $locationService->expects(self::once())
            ->method('loadParentLocationsForDraftContent')
            ->with($versionInfo)
            ->willReturn($locations);

        $viewParametersListener = new ViewParametersListener($locationService);

        $viewParametersListener->setViewTemplateParameters($event);

        $this->assertSame($locations, $contentView->getParameter('parentLocations'));
    }

    public function testSetViewTemplateParametersWithMainLocationId()
    {
        $mainLocationId = 123;

        $contentInfo = $this->generateContentInfo($mainLocationId);

        $versionInfo = $this->generateVersionInfo($contentInfo);

        $contentView = new ContentEditView();
        $contentView->setParameters([
            'content' => $this->generateContent($versionInfo),
            'parentLocations' => [],
        ]);

        $event = new PreContentViewEvent($contentView);

        $locationService = $this->createMock(LocationService::class);
        $locationService->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $viewParametersListener = new ViewParametersListener($locationService);

        $viewParametersListener->setViewTemplateParameters($event);

        $this->assertSame([], $contentView->getParameter('parentLocations'));
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

        $this->assertSame([MVCEvents::PRE_CONTENT_VIEW => 'setViewTemplateParameters'], $viewParametersListener::getSubscribedEvents());
    }

    /**
     * @param VersionInfo $versionInfo
     *
     * @return ApiContent
     */
    private function generateContent(VersionInfo $versionInfo): ApiContent
    {
        return new Content(['versionInfo' => $versionInfo]);
    }

    /**
     * @param ContentInfo $contentInfo
     *
     * @return APIVersionInfo
     */
    private function generateVersionInfo(APIContentInfo $contentInfo): APIVersionInfo
    {
        return new VersionInfo(['contentInfo' => $contentInfo]);
    }

    /**
     * @param int $mainLocationId
     *
     * @return ContentInfo
     */
    private function generateContentInfo(int $mainLocationId = null): APIContentInfo
    {
        return new ContentInfo(['mainLocationId' => $mainLocationId]);
    }
}
