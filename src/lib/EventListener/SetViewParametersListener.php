<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use eZ\Publish\Core\MVC\Symfony\View\View;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateView;
use EzSystems\EzPlatformContentForms\Content\View\ContentCreateView;
use EzSystems\EzPlatformContentForms\Content\View\ContentEditView;
use EzSystems\EzPlatformContentForms\User\View\UserUpdateView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @todo It should use ViewEvents::FILTER_VIEW_PARAMETERS event instead.
 */
class SetViewParametersListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\UserService */
    protected $userService;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(
        LocationService $locationService,
        UserService $userService,
        Repository $repository
    ) {
        $this->locationService = $locationService;
        $this->userService = $userService;
        $this->repository = $repository;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => [
                ['setContentEditViewTemplateParameters', 10],
                ['setUserUpdateViewTemplateParameters', 5],
                ['setContentTranslateViewTemplateParameters', 10],
                ['setContentCreateViewTemplateParameters', 10],
            ],
        ];
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent $event
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function setContentEditViewTemplateParameters(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof ContentEditView) {
            return;
        }

        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $contentView->getParameter('content');
        $location = $contentView->hasParameter('location') ? $contentView->getParameter('location') : null;
        $isPublished = null !== $content->contentInfo->mainLocationId && $content->contentInfo->published;

        $contentView->addParameters([
            'parent_location' => $this->resolveParentLocation($content, $location, $isPublished),
            'is_published' => $isPublished,
        ]);

        if (!$isPublished) {
            $contentView->addParameters([
                'parent_locations' => $this->locationService->loadParentLocationsForDraftContent($content->versionInfo),
            ]);
        }

        $contentInfo = $content->versionInfo->contentInfo;

        $this->processCreator($contentInfo, $contentView);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent $event
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function setContentTranslateViewTemplateParameters(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof ContentTranslateView) {
            return;
        }

        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $contentView->getContent();
        $location = $contentView->getLocation();
        $isPublished = null !== $content->contentInfo->mainLocationId && $content->contentInfo->published;

        $contentView->addParameters([
            'parent_location' => $this->resolveParentLocation($content, $location, $isPublished),
            'is_published' => $isPublished,
        ]);

        if (!$isPublished) {
            $contentView->addParameters([
                'parent_locations' => $this->locationService->loadParentLocationsForDraftContent($content->versionInfo),
            ]);
        }

        $contentInfo = $content->versionInfo->contentInfo;

        $this->processCreator($contentInfo, $contentView);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent $event
     */
    public function setUserUpdateViewTemplateParameters(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof UserUpdateView) {
            return;
        }

        /** @var \eZ\Publish\API\Repository\Values\User\User $user */
        $user = $contentView->getParameter('user');
        $contentInfo = $user->versionInfo->contentInfo;

        $this->processCreator($contentInfo, $contentView);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent $event
     */
    public function setContentCreateViewTemplateParameters(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof ContentCreateView) {
            return;
        }

        $contentView->addParameters([
            'content_create_struct' => $contentView->getForm()->getData(),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \eZ\Publish\Core\MVC\Symfony\View\View $contentView
     */
    private function processCreator(ContentInfo $contentInfo, View $contentView): void
    {
        try {
            $creator = $this->userService->loadUser($contentInfo->ownerId);
        } catch (NotFoundException $exception) {
            $creator = null;
        }

        $contentView->addParameters([
            'creator' => $creator,
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param bool $isPublished
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function resolveParentLocation(Content $content, ?Location $location, bool $isPublished): Location
    {
        if (!$isPublished) {
            $parentLocations = $this->repository->sudo(
                static function (Repository $repository) use ($content) {
                    return $repository->getLocationService()->loadParentLocationsForDraftContent($content->getVersionInfo());
                }
            );

            return reset($parentLocations);
        }

        if (null === $location) {
            throw new InvalidArgumentException('$location', 'You must provide a Location for the published Content item');
        }

        return $this->repository->sudo(
            static function (Repository $repository) use ($location) {
                return $repository->getLocationService()->loadLocation($location->parentLocationId);
            }
        );
    }
}
