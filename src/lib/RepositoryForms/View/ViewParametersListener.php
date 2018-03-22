<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\View;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewParametersListener implements EventSubscriberInterface
{
    /** @var LocationService */
    protected $locationService;

    /**
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [MVCEvents::PRE_CONTENT_VIEW => 'setViewTemplateParameters'];
    }

    /**
     * @param PreContentViewEvent $event
     */
    public function setViewTemplateParameters(PreContentViewEvent $event)
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof ContentEditView) {
            return;
        }

        $content = $contentView->getParameter('content');
        $location = $contentView->hasParameter('location') ? $contentView->getParameter('location') : null;
        $isPublished = null !== $content->contentInfo->mainLocationId && $content->contentInfo->published;

        $contentView->addParameters([
            'parentLocation' => $this->resolveParentLocation($content, $location, $isPublished),
            'isPublished' => $isPublished,
        ]);

        if (!$isPublished) {
            $contentView->addParameters([
                'parentLocations' => $this->locationService->loadParentLocationsForDraftContent($content->versionInfo),
            ]);
        }
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param bool $isPublished
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function resolveParentLocation(Content $content, ?Location $location, bool $isPublished): Location
    {
        if (!$isPublished) {
            $parentLocations = $this->locationService->loadParentLocationsForDraftContent($content->getVersionInfo());

            return reset($parentLocations);
        }

        if (null === $location) {
            throw new InvalidArgumentException('$location', 'Location for published content has to be provided');
        }

        return $this->locationService->loadLocation($location->parentLocationId);
    }
}
