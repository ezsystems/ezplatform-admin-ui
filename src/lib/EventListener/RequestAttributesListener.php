<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\Repository;

/**
 * Collects parameters for the ViewBuilder from the Request.
 */
class RequestAttributesListener implements EventSubscriberInterface
{
    /** @var Repository */
    private $repository;

    /** @var array */
    private $siteAccessGroups;

    /**
     * @param array $siteAccessGroups
     * @param Repository $repository
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
     * @param FilterViewBuilderParametersEvent $event
     *
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function addRequestAttributes(FilterViewBuilderParametersEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->isAdmin($request)) {
            return;
        }

        $parameterBag = $event->getParameters();

        if ($parameterBag->has('locationId') && null !== $parameterBag->get('locationId')) {
            $location = $this->loadLocation($parameterBag->get('locationId'));
            $parameterBag->set('location', $location);
        }

        if ($this->hasContentLanguage($request, $parameterBag)) {
            /** @var Location $location */
            $location = $parameterBag->get('location');

            $languageCode = $parameterBag->get('languageCode');

            $content = $this->loadContent($location->contentInfo->id, $languageCode);
            $parameterBag->set('content', $content);
        }
    }

    /**
     * @param Request $request
     * @param ParameterBag $parameterBag
     *
     * @return bool
     */
    private function hasContentLanguage(Request $request, ParameterBag $parameterBag): bool
    {
        return $parameterBag->has('languageCode') && $parameterBag->has('location') && $request->get('_route') === '_ezpublishLocation';
    }

    /**
     * @param $locationId
     *
     * @return Location
     */
    private function loadLocation($locationId): Location
    {
        $location = $this->repository->sudo(
            function (Repository $repository) use ($locationId) {
                return $repository->getLocationService()->loadLocation($locationId);
            }
        );

        return $location;
    }

    /**
     * @param int $contentId
     * @param string $language
     *
     * @return Content
     *
     * @throws UnauthorizedException
     * @throws NotFoundException
     */
    private function loadContent(int $contentId, ?string $language): Content
    {
        return $this->repository->getContentService()->loadContent($contentId, $language ? [$language] : null);
    }

    private function isAdmin(Request $request): bool
    {
        $siteAccess = $request->attributes->get('siteaccess');

        return in_array($siteAccess->name, $this->siteAccessGroups[EzPlatformAdminUiBundle::ADMIN_GROUP_NAME], true);
    }
}
