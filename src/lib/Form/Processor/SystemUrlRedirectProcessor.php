<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Processor;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class SystemUrlRedirectProcessor implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param array $siteaccessGroups
     */
    public function __construct(
        RouterInterface $router,
        URLAliasService $urlAliasService,
        LocationService $locationService
    ) {
        $this->router = $router;
        $this->urlAliasService = $urlAliasService;
        $this->locationService = $locationService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['processRedirectAfterPublish', 2],
            RepositoryFormEvents::CONTENT_CANCEL => ['processRedirectAfterCancel', 2],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processRedirectAfterPublish(FormActionEvent $event): void
    {
        if ($event->getForm()['redirectUrlAfterPublish']->getData()) {
            return;
        }

        $this->resolveSystemUrlRedirect($event);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processRedirectAfterCancel(FormActionEvent $event): void
    {
        $this->resolveSystemUrlRedirect($event);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function resolveSystemUrlRedirect(FormActionEvent $event): void
    {
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
