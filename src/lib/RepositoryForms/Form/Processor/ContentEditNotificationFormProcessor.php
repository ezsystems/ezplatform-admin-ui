<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor;

use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ContentEditNotificationFormProcessor implements EventSubscriberInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var array */
    private $siteAccessGroups;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param array $siteAccessGroups
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        RequestStack $requestStack,
        array $siteAccessGroups
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->requestStack = $requestStack;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['addPublishMessage', 5],
            RepositoryFormEvents::CONTENT_SAVE_DRAFT => ['addSaveDraftMessage', 5],
        ];
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function addPublishMessage(FormActionEvent $event)
    {
        if (!$this->isAdminSiteAccess($this->requestStack->getCurrentRequest())) {
            return;
        }
        $this->notificationHandler->success(
            /** @Desc("Content published.") */
            'content.published.success',
            [],
            'content_edit'
        );
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function addSaveDraftMessage(FormActionEvent $event)
    {
        if (!$this->isAdminSiteAccess($this->requestStack->getCurrentRequest())) {
            return;
        }
        $this->notificationHandler->success(
            /** @Desc("Content draft saved.") */
            'content.draft_saved.success',
            [],
            'content_edit'
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    protected function isAdminSiteAccess(Request $request): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
