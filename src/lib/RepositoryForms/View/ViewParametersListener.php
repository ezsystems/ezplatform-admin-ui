<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\View;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
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

        /** @var Content $content */
        $content = $contentView->getParameter('content');
        $mainLocationId = $content->versionInfo->contentInfo->mainLocationId;

        /** Not part of tree yet */
        if (empty($mainLocationId)) {
            $parentLocations = $this->locationService->loadParentLocationsForDraftContent($content->versionInfo);

            $contentView->addParameters([
                'parentLocations' => $parentLocations,
            ]);
        }
    }
}
