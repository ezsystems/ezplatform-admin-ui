<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor\Content;

use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlRedirectProcessor implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    /** @var array */
    private $siteaccessGroups;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     * @param \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteaccess
     * @param array $siteaccessGroups
     */
    public function __construct(
        RouterInterface $router,
        URLAliasService $urlAliasService,
        SiteAccess $siteaccess,
        array $siteaccessGroups
    ) {
        $this->router = $router;
        $this->urlAliasService = $urlAliasService;
        $this->siteaccess = $siteaccess;
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
     */
    public function processRedirectAfterPublish(FormActionEvent $event): void
    {
        if ($event->getForm()['redirectUrlAfterPublish']->getData()) {
            return;
        }

        $this->resolveAdminSiteaccessRedirect($event);
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function processRedirectAfterCancel(FormActionEvent $event): void
    {
        $this->resolveAdminSiteaccessRedirect($event);
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
     */
    private function resolveAdminSiteaccessRedirect(FormActionEvent $event): void
    {
        if (!$this->isAdminSiteaccess()) {
            return;
        }

        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
        $response = $event->getResponse();

        if (!$response instanceof RedirectResponse) {
            return;
        }

        $location = $event->getOption('referrerLocation');

        $targetUrl = $location instanceof Location
            ? $this->router->generate(
                '_ezpublishLocation',
                [
                    'locationId' => $location->id,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            : $this->router->generate(
                'ezplatform.dashboard',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

        $event->setResponse(new RedirectResponse($targetUrl));
    }
}
