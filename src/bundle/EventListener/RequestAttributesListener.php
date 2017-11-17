<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\EventListener;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use EzSystems\EzPlatformAdminUi\SiteAccess\AdminFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\Repository;

/**
 * Collects parameters for the ViewBuilder from the Request.
 */
class RequestAttributesListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\Repository */
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
     * @param \eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent $e
     */
    public function addRequestAttributes(FilterViewBuilderParametersEvent $e)
    {
        if (!$this->isAdmin($e->getRequest())) {
            return;
        }

        $parameterBag = $e->getParameters();

        if ($parameterBag->has('locationId')) {
            $location = $this->loadLocation($parameterBag->get('locationId'));
            $parameterBag->remove('locationId');

            $parameterBag->set('location', $location);
        }
    }

    private function loadLocation($locationId): Location
    {
        $location = $this->repository->sudo(
            function (Repository $repository) use ($locationId) {
                return $repository->getLocationService()->loadLocation($locationId);
            }
        );

        return $location;
    }

    private function isAdmin(Request $request): bool
    {
        $siteAccess = $request->attributes->get('siteaccess');

        return in_array($siteAccess->name, $this->siteAccessGroups[AdminFilter::ADMIN_GROUP_NAME], true);
    }
}
