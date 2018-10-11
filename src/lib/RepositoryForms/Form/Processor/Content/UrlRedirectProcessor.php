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
use EzSystems\RepositoryForms\Form\Processor\SystemUrlRedirectProcessor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UrlRedirectProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    /** @var \EzSystems\RepositoryForms\Form\Processor\SystemUrlRedirectProcessor */
    private $systemUrlRedirectProcessor;

    /** @var array */
    private $siteaccessGroups;

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteaccess
     * @param \EzSystems\RepositoryForms\Form\Processor\SystemUrlRedirectProcessor $systemUrlRedirectProcessor
     * @param array $siteaccessGroups
     */
    public function __construct(
        SiteAccess $siteaccess,
        SystemUrlRedirectProcessor $systemUrlRedirectProcessor,
        array $siteaccessGroups
    ) {
        $this->siteaccess = $siteaccess;
        $this->systemUrlRedirectProcessor = $systemUrlRedirectProcessor;
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

        if ($this->isAdminSiteaccess()) {
            return;
        }

        $this->systemUrlRedirectProcessor->processRedirectAfterPublish($event);
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
        if ($this->isAdminSiteaccess()) {
            return;
        }

        $this->systemUrlRedirectProcessor->processRedirectAfterCancel($event);
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
}
