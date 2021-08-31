<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Collects parameters for the ViewBuilder from the Request.
 */
class RequestAttributesListener implements EventSubscriberInterface
{
    private const TRANSLATED_CONTENT_VIEW_ROUTE_NAME = '_ez_content_translation_view';

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var array */
    private $siteAccessGroups;

    /**
     * @param array $siteAccessGroups
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(array $siteAccessGroups, Repository $repository)
    {
        $this->repository = $repository;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public static function getSubscribedEvents()
    {
        return [ViewEvents::FILTER_BUILDER_PARAMETERS => 'addRequestAttributes'];
    }

    /**
     * Adds all the request attributes to the parameters.
     *
     * @param \eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function addRequestAttributes(FilterViewBuilderParametersEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->isAdmin($request)) {
            return;
        }

        $parameterBag = $event->getParameters();

        if ($parameterBag->has('locationId') && null !== $parameterBag->get('locationId')) {
            $location = $this->loadLocation((int)$parameterBag->get('locationId'));
            $parameterBag->set('location', $location);
        }

        if ($this->hasContentLanguage($request, $parameterBag)) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $parameterBag->get('location');

            $languageCode = $parameterBag->get('languageCode');

            $content = $this->loadContent($location->contentInfo->id, $languageCode);
            $parameterBag->set('content', $content);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\ParameterBag $parameterBag
     *
     * @return bool
     */
    private function hasContentLanguage(Request $request, ParameterBag $parameterBag): bool
    {
        return $parameterBag->has('languageCode')
            && $parameterBag->has('location')
            && $request->get('_route') === self::TRANSLATED_CONTENT_VIEW_ROUTE_NAME;
    }

    /**
     * @param int $locationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function loadLocation(int $locationId): Location
    {
        $location = $this->repository->sudo(
            static function (Repository $repository) use ($locationId) {
                return $repository->getLocationService()->loadLocation($locationId);
            }
        );

        return $location;
    }

    /**
     * @param int $contentId
     * @param string $language
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadContent(int $contentId, ?string $language): Content
    {
        return $this->repository->getContentService()->loadContent($contentId, $language ? [$language] : null);
    }

    private function isAdmin(Request $request): bool
    {
        $siteAccess = $request->attributes->get('siteaccess');

        return \in_array($siteAccess->name, $this->siteAccessGroups[EzPlatformAdminUiBundle::ADMIN_GROUP_NAME], true);
    }
}
