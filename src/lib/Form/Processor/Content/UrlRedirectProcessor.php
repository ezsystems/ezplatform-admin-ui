<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Processor\Content;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\EzPlatformContentForms\Event\ContentFormEvents;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use EzSystems\EzPlatformContentForms\Form\Processor\SystemUrlRedirectProcessor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UrlRedirectProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    /** @var \EzSystems\EzPlatformContentForms\Form\Processor\SystemUrlRedirectProcessor */
    private $systemUrlRedirectProcessor;

    /** @var array */
    private $siteaccessGroups;

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\SiteAccess $siteaccess
     * @param \EzSystems\EzPlatformContentForms\Form\Processor\SystemUrlRedirectProcessor $systemUrlRedirectProcessor
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
            ContentFormEvents::CONTENT_PUBLISH => ['processRedirectAfterPublish', 0],
            ContentFormEvents::CONTENT_CANCEL => ['processRedirectAfterCancel', 0],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformContentForms\Event\FormActionEvent $event
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
     * @param \EzSystems\EzPlatformContentForms\Event\FormActionEvent $event
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
