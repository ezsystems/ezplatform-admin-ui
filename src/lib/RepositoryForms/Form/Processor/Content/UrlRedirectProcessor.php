<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor\Content;

use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlRedirectProcessor implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var array */
    private $siteaccessGroups;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param array $siteaccessGroups
     */
    public function __construct(
        RouterInterface $router,
        RequestStack $requestStack,
        array $siteaccessGroups
    ) {
        $this->router = $router;
        $this->requestStack = $requestStack;
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
        if (!$this->isAdminSiteAccess()) {
            return;
        }

        if (null === $event->getLocationToRedirect()) {
            return;
        }

        if ($event->getForm()['redirectUrlAfterPublish']->getData()) {
            return;
        }

        $targetLocation = $event->getLocationToRedirect();
        $response = new RedirectResponse($this->router->generate(
            '_ezpublishLocation',
            ['locationId' => $targetLocation->id],
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        $event->setResponse($response);
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function processRedirectAfterCancel(FormActionEvent $event): void
    {
        if (!$this->isAdminSiteAccess()) {
            return;
        }

        /** @var \eZ\Publish\API\Repository\Values\Content\Location $targetLocation */
        $targetLocation = $event->getLocationToRedirect();
        $response = new RedirectResponse($this->router->generate(
            '_ezpublishLocation',
            ['locationId' => $targetLocation->id],
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
        $event->setResponse($response);
    }

    /**
     * @return bool
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    protected function isAdminSiteAccess(): bool
    {
        $siteaccess = $this->requestStack->getCurrentRequest()->attributes->get('siteaccess');

        return (new IsAdmin($this->siteaccessGroups))->isSatisfiedBy($siteaccess);
    }
}
