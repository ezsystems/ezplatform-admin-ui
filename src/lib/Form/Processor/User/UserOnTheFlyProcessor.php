<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Processor\User;

use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\Event\UserOnTheFlyEvents;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\CreateUserOnTheFlyDispatcher;
use EzSystems\EzPlatformContentForms\Data\User\UserCreateData;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class UserOnTheFlyProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var Environment */
    private $twig;

    /**
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \Twig\Environment $twig
     */
    public function __construct(UserService $userService, Environment $twig)
    {
        $this->userService = $userService;
        $this->twig = $twig;
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
        ];
    }

    /**d
     * @param \EzSystems\EzPlatformContentForms\Event\FormActionEvent $event
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
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
                $this->twig->render('@ezdesign/ui/on_the_fly/user_create_on_the_fly.html.twig', [
                    'locationId' => $user->contentInfo->mainLocationId,
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
