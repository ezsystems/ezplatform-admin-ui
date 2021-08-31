<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\EventListener;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content as API;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\Repository\Values\Content as Core;
use eZ\Publish\Core\Repository\Values\User\User as CoreUser;
use EzSystems\EzPlatformAdminUi\EventListener\SetViewParametersListener;
use EzSystems\EzPlatformContentForms\Content\View\ContentEditView;
use EzSystems\EzPlatformContentForms\User\View\UserUpdateView;
use PHPUnit\Framework\TestCase;

final class SetViewParametersListenerTest extends TestCase
{
    private const EXAMPLE_LOCATION_A_ID = 1;
    private const EXAMPLE_LOCATION_B_ID = 2;
    private const EXAMPLE_OWNER_ID = 14;

    /** @var \eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent */
    private $event;

    /** @var \EzSystems\EzPlatformAdminUi\EventListener\SetViewParametersListener */
    private $viewParametersListener;

    /** @var \eZ\Publish\API\Repository\LocationService|\PHPUnit\Framework\MockObject\MockObject */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private $userService;

    /** @var \eZ\Publish\API\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject */
    private $repository;

    public function setUp(): void
    {
        $contentInfo = $this->generateContentInfo();

        $versionInfo = $this->generateVersionInfo($contentInfo);

        $contentView = new ContentEditView();
        $contentView->setParameters(['content' => $this->generateContent($versionInfo)]);

        $this->event = new PreContentViewEvent($contentView);

        $this->locationService = $this->createMock(LocationService::class);
        $this->userService = $this->createMock(UserService::class);
        $this->repository = $this->createMock(Repository::class);

        $this->viewParametersListener = new SetViewParametersListener(
            $this->locationService,
            $this->userService,
            $this->repository
        );
    }

    public function testSetViewTemplateParameters()
    {
        $locationA = new Core\Location(['id' => self::EXAMPLE_LOCATION_A_ID]);
        $locationB = new Core\Location(['id' => self::EXAMPLE_LOCATION_B_ID]);
        $locations = [$locationA, $locationB];

        $contentInfo = $this->generateContentInfo();

        $versionInfo = $this->generateVersionInfo($contentInfo);
        $content = $this->generateContent($versionInfo);
        $location = $this->generateLocation();

        $contentView = new ContentEditView();
        $contentView->setParameters([
            'content' => $content,
            'location' => $location,
        ]);

        $this->locationService
            ->method('loadParentLocationsForDraftContent')
            ->with($versionInfo)
            ->willReturn($locations);

        $this->repository
            ->method('sudo')
            ->willReturn([$locationA]);

        $this->viewParametersListener->setContentEditViewTemplateParameters(new PreContentViewEvent($contentView));

        $this->assertSame($locations, $contentView->getParameter('parent_locations'));
    }

    /**
     * @param int|null $parentLocationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function generateLocation(int $parentLocationId = null): API\Location
    {
        return new Core\Location(['id' => 3, 'parentLocationId' => $parentLocationId]);
    }

    public function testSetViewTemplateParametersWithMainLocationId()
    {
        $mainLocationId = 123;
        $parentLocationId = 456;
        $published = true;

        $parentLocation = new Core\Location(['id' => $parentLocationId]);
        $parentLocations = [$parentLocation];
        $contentInfo = $this->generateContentInfo($mainLocationId, $published);
        $versionInfo = $this->generateVersionInfo($contentInfo);
        $content = $this->generateContent($versionInfo);
        $location = $this->generateLocation($parentLocationId);

        $contentView = new ContentEditView();
        $contentView->setParameters([
            'content' => $content,
            'location' => $location,
            'parent_locations' => [],
        ]);

        $this->locationService
            ->method('loadParentLocationsForDraftContent')
            ->with($versionInfo)
            ->willReturn($parentLocations);
        $this->locationService
            ->method('loadLocation')
            ->with($parentLocationId)
            ->willReturn(reset($parentLocations));
        $this->repository
            ->method('sudo')
            ->willReturn($parentLocation);

        $this->viewParametersListener->setContentEditViewTemplateParameters(new PreContentViewEvent($contentView));

        $this->assertSame([], $contentView->getParameter('parent_locations'));
        $this->assertSame(reset($parentLocations), $contentView->getParameter('parent_location'));
    }

    public function testSetViewTemplateParametersWithoutContentEditViewInstance()
    {
        $contentView = $this->createMock(View::class);

        $this->locationService->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $this->assertNull(
            $this->viewParametersListener->setContentEditViewTemplateParameters(
                new PreContentViewEvent($contentView)
            )
        );
    }

    public function testSetUserUpdateViewTemplateParametersWithoutUserUpdateViewInstance()
    {
        $view = $this->createMock(View::class);

        $this->locationService->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $this->assertNull(
            $this->viewParametersListener->setUserUpdateViewTemplateParameters(
                new PreContentViewEvent($view)
            )
        );
    }

    public function testSetUserUpdateViewTemplateParameters()
    {
        $ownerId = 42;

        $user = $this->generateUser($ownerId);

        $userUpdateView = new UserUpdateView();
        $userUpdateView->setParameters([
            'user' => $user,
        ]);

        $this->userService
            ->method('loadUser')
            ->with($ownerId)
            ->willReturn($user);

        $this->viewParametersListener->setUserUpdateViewTemplateParameters(new PreContentViewEvent($userUpdateView));

        $this->assertSame($user, $userUpdateView->getParameter('creator'));
    }

    public function testSubscribedEvents()
    {
        $this->locationService
            ->expects(self::never())
            ->method('loadParentLocationsForDraftContent');

        $expectedSubscribedEvents = [
            MVCEvents::PRE_CONTENT_VIEW => [
                ['setContentEditViewTemplateParameters', 10],
                ['setUserUpdateViewTemplateParameters', 5],
                ['setContentTranslateViewTemplateParameters', 10],
                ['setContentCreateViewTemplateParameters', 10],
            ],
        ];

        $actualSubscribedEvents = $this->viewParametersListener::getSubscribedEvents();

        $this->assertCount(count($actualSubscribedEvents), $expectedSubscribedEvents);
        foreach ($expectedSubscribedEvents as $key => $value) {
            $this->assertArrayHasKey($key, $actualSubscribedEvents);
            $this->assertSame($value, $actualSubscribedEvents[$key]);
        }
    }

    /**
     * @param int $mainLocationId
     * @param bool $published
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    private function generateContentInfo(int $mainLocationId = null, bool $published = false): API\ContentInfo
    {
        return new API\ContentInfo([
            'mainLocationId' => $mainLocationId,
            'ownerId' => self::EXAMPLE_OWNER_ID,
            'published' => $published,
        ]);
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
