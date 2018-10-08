<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor\Content;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class UrlRedirectProcessor implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var array */
    private $siteaccessGroups;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     * @param \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteaccess
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param array $siteaccessGroups
     */
    public function __construct(
        RouterInterface $router,
        URLAliasService $urlAliasService,
        SiteAccess $siteaccess,
        LocationService $locationService,
        array $siteaccessGroups
    ) {
        $this->router = $router;
        $this->urlAliasService = $urlAliasService;
        $this->siteaccess = $siteaccess;
        $this->locationService = $locationService;
        $this->siteaccessGroups = $siteaccessGroups;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['processRedirectAfterPublish', 0],
            RepositoryFormEvents::CONTENT_CANCEL => ['processRedirectAfterCancel', 0],
        ];
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processRedirectAfterPublish(FormActionEvent $event): void
    {
        if ($event->getForm()['redirectUrlAfterPublish']->getData()) {
            return;
        }

        $this->resolveNonAdminSiteaccessRedirect($event);
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processRedirectAfterCancel(FormActionEvent $event): void
    {
        $this->resolveNonAdminSiteaccessRedirect($event);
    }

    /**
     * @return bool
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    protected function isAdminSiteaccess(): bool
    {
        return (new IsAdmin($this->siteaccessGroups))->isSatisfiedBy($this->siteaccess);
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function resolveNonAdminSiteaccessRedirect(FormActionEvent $event): void
    {
        if ($this->isAdminSiteaccess()) {
            return;
        }

        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
        $response = $event->getResponse();

        if (!$response instanceof RedirectResponse) {
            return;
        }

        $params = $this->router->match($response->getTargetUrl());

        if (!in_array('locationId', $params)) {
            return;
        }

        $location = $this->locationService->loadLocation($params['locationId']);

        $event->setResponse(new RedirectResponse($this->urlAliasService->reverseLookup($location)->path));
    }
}
