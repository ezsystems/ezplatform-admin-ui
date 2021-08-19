<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\AdminUi\Controller\User;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Ibexa\AdminUi\Form\Data\User\UserDeleteData;
use Ibexa\AdminUi\Form\Factory\FormFactory;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\LocationService;

class UserDeleteController extends Controller
{
    /** @var TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var UserService */
    private $userService;

    /** @var LocationService */
    private $locationService;

    /**
     * @param TranslatableNotificationHandlerInterface $notificationHandler
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param UserService $userService
     * @param LocationService $locationService
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        UserService $userService,
        LocationService $locationService
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->userService = $userService;
        $this->locationService = $locationService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws NotFoundException
     */
    public function userDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->deleteUser();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (UserDeleteData $data) {
                $contentInfo = $data->getContentInfo();

                $location = $this->locationService->loadLocation($contentInfo->mainLocationId);
                $parentLocation = $this->locationService->loadLocation($location->parentLocationId);

                $user = $this->userService->loadUser($contentInfo->id);

                $this->userService->deleteUser($user);

                $this->notificationHandler->success(
                    /** @Desc("User with login '%login%' deleted.") */
                    'user.delete.success',
                    ['%login%' => $user->login],
                    'content'
                );

                return new RedirectResponse($this->generateUrl('_ez_content_view', [
                    'contentId' => $parentLocation->contentId,
                    'locationId' => $location->parentLocationId,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.dashboard'));
    }
}

class_alias(UserDeleteController::class, 'EzSystems\EzPlatformAdminUiBundle\Controller\User\UserDeleteController');
