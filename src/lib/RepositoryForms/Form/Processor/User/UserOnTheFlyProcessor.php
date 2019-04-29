<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor\User;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Dispatcher\UserOnTheFlyDispatcher;
use EzSystems\RepositoryForms\Data\User\UserCreateData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class UserOnTheFlyProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var Environment */
    private $twig;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \Twig\Environment $twig
     */
    public function __construct(
        UserService $userService,
        ContentService $contentService,
        LocationService $locationService,
        Environment $twig
    ) {
        $this->userService = $userService;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
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
            UserOnTheFlyDispatcher::EVENT_BASE_NAME . '.create' => ['processCreate', 10],
        ];
    }

    /**d
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
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
        $user = $this->contentService->createContent(
            $data,
            $this->createLocationCreateStructsFromUserGroups(
                $data->getParentGroups()
            )
        );

        $event->setResponse(
            new Response(
                $this->twig->render('@ezdesign/ui/on_the_fly/content_create_response.html.twig', [
                    'locationId' => $user->contentInfo->mainLocationId,
                ])
            )
        );
    }

    private function createLocationCreateStructsFromUserGroups(array $userGroups): array
    {
        $locationCreateStructs = [];
        foreach ($userGroups as $parentGroup) {
            $parentGroup = $this->userService->loadUserGroup($parentGroup->id);
            if ($parentGroup->getVersionInfo()->getContentInfo()->mainLocationId !== null) {
                $locationCreateStructs[] = $this->locationService->newLocationCreateStruct(
                    $parentGroup->getVersionInfo()->getContentInfo()->mainLocationId
                );
            }
        }

        return $locationCreateStructs;
    }

    /**
     * @param \EzSystems\RepositoryForms\Data\User\UserCreateData $data
     * @param string $languageCode
     */
    private function setContentFields(UserCreateData $data, string $languageCode): void
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }
    }
}
