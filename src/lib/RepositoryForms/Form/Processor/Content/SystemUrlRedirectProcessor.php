<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor\Content;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class SystemUrlRedirectProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var array */
    private $siteaccessGroups;

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteaccess
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param array $siteaccessGroups
     */
    public function __construct(
        SiteAccess $siteaccess,
        RouterInterface $router,
        array $siteaccessGroups
    ) {
        $this->siteaccess = $siteaccess;
        $this->router = $router;
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

        if (!$this->isAdminSiteaccess()) {
            return;
        }

        $this->resolveSystemUrlRedirect($event);
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
        if (!$this->isAdminSiteaccess()) {
            return;
        }
        
        $this->resolveSystemUrlRedirect($event);
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

        if (!$event->hasPayload('content')) {
            return;
        }

        $locationId = $event->getPayload('content')->versionInfo->contentInfo->mainLocationId;

        $url = $this->router->generate(
            '_ezpublishLocation', ['locationId' => $locationId]
        );

        $event->setResponse(new RedirectResponse($url));
    }
}

