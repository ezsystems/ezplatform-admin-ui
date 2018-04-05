<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor;

use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class ContentEditNotificationFormProcessor implements EventSubscriberInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var array */
    private $siteAccessGroups;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param array $siteAccessGroups
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        array $siteAccessGroups
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
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
            $this->translator->trans(
                /** @Desc("Content published.") */
                'content.published.success',
                [],
                'content_edit'
            )
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
            $this->translator->trans(
                /** @Desc("Content draft saved.") */
                'content.draft_saved.success',
                [],
                'content_edit'
            )
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
