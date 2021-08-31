<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Processor\User;

use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\Event\UserOnTheFlyEvents;
use EzSystems\EzPlatformContentForms\Data\User\UserCreateData;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use EzSystems\EzPlatformContentForms\Form\Processor\User\UserUpdateFormProcessor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class UserOnTheFlyProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \Twig\Environment */
    private $twig;

    /** @var \EzSystems\EzPlatformContentForms\Form\Processor\User\UserUpdateFormProcessor */
    private $innerUserUpdateFormProcessor;

    public function __construct(
        UserService $userService,
        Environment $twig,
        UserUpdateFormProcessor $innerUserUpdateFormProcessor
    ) {
        $this->userService = $userService;
        $this->twig = $twig;
        $this->innerUserUpdateFormProcessor = $innerUserUpdateFormProcessor;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            UserOnTheFlyEvents::USER_CREATE_PUBLISH => ['processCreate', 10],
            UserOnTheFlyEvents::USER_EDIT_PUBLISH => ['processEdit', 10],
        ];
    }

    public function processCreate(FormActionEvent $event)
    {
        $data = $data = $event->getData();

        if (!$data instanceof UserCreateData) {
            return;
        }

        $form = $event->getForm();
        $languageCode = $form->getConfig()->getOption('languageCode');

        $this->setContentFields($data, $languageCode);
        $user = $this->userService->createUser($data, $data->getParentGroups());

        $event->setResponse(
            new Response(
                $this->twig->render('@ezdesign/ui/on_the_fly/user_create_response.html.twig', [
                    'locationId' => $user->contentInfo->mainLocationId,
                ])
            )
        );
    }

    public function processEdit(FormActionEvent $event): void
    {
        // Rely on User Form Processor from ContentForms to avoid unncessary code duplication
        $this->innerUserUpdateFormProcessor->processUpdate($event);

        $referrerLocation = $event->getOption('referrerLocation');

        // We only need to change the response so it's compatible with UDW
        $event->setResponse(
            new Response(
                $this->twig->render('@ezdesign/ui/on_the_fly/user_edit_response.html.twig', [
                    'locationId' => $referrerLocation->id,
                ])
            )
        );
    }

    /**
     * @param \EzSystems\EzPlatformContentForms\Data\User\UserCreateData $data
     * @param string $languageCode
     */
    private function setContentFields(UserCreateData $data, string $languageCode): void
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }
    }
}
