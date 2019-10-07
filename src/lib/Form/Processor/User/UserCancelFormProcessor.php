<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Processor\User;

use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\User\UserCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\User\UserUpdateData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listens for and processes User cancel events.
 */
class UserCancelFormProcessor implements EventSubscriberInterface
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::USER_CANCEL => ['processCancel', 10],
        ];
    }

    public function processCancel(FormActionEvent $event)
    {
        /** @var UserCreateData|UserUpdateData $data */
        $data = $event->getData();

        $locationId = $data->isNew()
            ? $data->getParentGroups()[0]->contentInfo->mainLocationId
            : $data->user->contentInfo->mainLocationId;

        $response = new RedirectResponse($this->urlGenerator->generate('_ezpublishLocation', [
            'locationId' => $locationId,
        ]));
        $event->setResponse($response);
    }
}
