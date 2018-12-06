<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\RepositoryForms\View;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content as API;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\Repository\Values\Content as Core;
use eZ\Publish\Core\Repository\Values\User\User as CoreUser;
use EzSystems\EzPlatformAdminUi\RepositoryForms\View\ViewParametersListener;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use EzSystems\RepositoryForms\User\View\UserUpdateView;
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

        $userService = $this->createMock(UserService::class);

        $viewParametersListener = new ViewParametersListener($locationService, $userService);
        $viewParametersListener->setContentEditViewTemplateParameters($event);

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

        $userService = $this->createMock(UserService::class);

        $viewParametersListener = new ViewParametersListener($locationService, $userService);
        $viewParametersListener->setContentEditViewTemplateParameters($event);

        $this->assertSame([], $contentView->getParameter('parentLocations'));
        $this->assertSame(reset($parentLocations), $contentView->getParameter('parentLocation'));
    }

    public function testSetViewTemplateParametersWithoutContentEditViewInstance()
    {
        $contentView = $this->createMock(View::class);

        $locationService = $this->createMock(LocationService::class);
        $locationService->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $userService = $this->createMock(UserService::class);

        $viewParametersListener = new ViewParametersListener($locationService, $userService);

        $this->assertNull($viewParametersListener->setContentEditViewTemplateParameters(new PreContentViewEvent($contentView)));
    }

    public function testSetUserUpdateViewTemplateParametersWithoutUserUpdateViewInstance()
    {
        $view = $this->createMock(View::class);

        $locationService = $this->createMock(LocationService::class);
        $locationService->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $userService = $this->createMock(UserService::class);

        $viewParametersListener = new ViewParametersListener($locationService, $userService);

        $this->assertNull($viewParametersListener->setUserUpdateViewTemplateParameters(new PreContentViewEvent($view)));
    }

    public function testSetUserUpdateViewTemplateParameters()
    {
        $ownerId = 42;

        $user = $this->generateUser($ownerId);

        $userUpdateView = new UserUpdateView();
        $userUpdateView->setParameters([
            'user' => $user,
        ]);

        $event = new PreContentViewEvent($userUpdateView);

        $locationService = $this->createMock(LocationService::class);

        $userService = $this->createMock(UserService::class);
        $userService
            ->method('loadUser')
            ->with($ownerId)
            ->willReturn($user);

        $viewParametersListener = new ViewParametersListener($locationService, $userService);
        $viewParametersListener->setUserUpdateViewTemplateParameters($event);

        $this->assertSame($user, $userUpdateView->getParameter('creator'));
    }

    public function testSubscribedEvents()
    {
        $locationService = $this->createMock(LocationService::class);
        $locationService->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $userService = $this->createMock(UserService::class);

        $viewParametersListener = new ViewParametersListener($locationService, $userService);

        $expectedSubscribedEvents = [
            MVCEvents::PRE_CONTENT_VIEW => [
                ['setContentEditViewTemplateParameters', 10],
                ['setUserUpdateViewTemplateParameters', 5],
                ['setContentTranslateViewTemplateParameters', 10],
                ['setContentCreateViewTemplateParameters', 10],
            ],
        ];

        $this->assertArraySubset($expectedSubscribedEvents, $viewParametersListener::getSubscribedEvents());
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

    /**
     * @param int $ownerId
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    private function generateUser(int $ownerId): APIUser
    {
        $contentInfo = new API\ContentInfo(['ownerId' => $ownerId]);

        $versionInfo = new Core\VersionInfo(['contentInfo' => $contentInfo]);

        $content = $this->generateContent($versionInfo);

        return new CoreUser(['content' => $content]);
    }
}
