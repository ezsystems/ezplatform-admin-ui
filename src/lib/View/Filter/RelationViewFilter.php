<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Filter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use EzSystems\EzPlatformAdminUi\View\RelationView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RelationViewFilter implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        ContentTypeService $contentTypeService
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => [
                ['onPreRelationView', 0],
            ],
        ];
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function onPreRelationView(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();

        if (!$view instanceof RelationView) {
            return;
        }

        if (!$view->hasParameter('contentId')) {
            return;
        }

        $parameters = $event->getContentView()->getParameters();

        if ($parameters['contentId'] === null) {
            throw new InvalidArgumentException(
                'Content',
                'No content could be loaded from parameters'
            );
        }

        $contentId = (int)$parameters['contentId'];

        try {
            $content = $this->contentService->loadContent($contentId);
            $location = $this->locationService->loadLocation($content->contentInfo->mainLocationId);
            $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        } catch (UnauthorizedException $exception) {
            $view->setTemplateIdentifier(
                '@ezdesign/content/relation_unauthorized.html.twig'
            );

            return;
        }

        $view->addParameters([
            'content' => $content,
            'location' => $location,
            'contentType' => $contentType,
        ]);
    }
}
