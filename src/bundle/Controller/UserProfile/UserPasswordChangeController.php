<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\UserProfile;

use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException;
use eZ\Publish\API\Repository\Exceptions\ContentValidationException;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\LanguageService;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

class UserPasswordChangeController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var LanguageService */
    private $userService;

    /** @var FormFactory */
    private $formFactory;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param UserService $userService
     * @param FormFactory $formFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        UserService $userService,
        FormFactory $formFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->userService = $userService;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     * @throws ContentValidationException
     * @throws ContentFieldValidationException
     * @throws InvalidOptionsException
     */
    public function userPasswordChangeAction(Request $request): Response
    {
        $form = $this->formFactory->changeUserPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $newPassword = $data->getNewPassword();
                $userUpdateStruct = $this->userService->newUserUpdateStruct();
                $userUpdateStruct->password = $newPassword;
                $user = $this->tokenStorage->getToken()->getUser()->getAPIUser();

                $this->userService->updateUser($user, $userUpdateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Your password has been successfully changed.") */
                        'user.change_password.success',
                        [],
                        'user_change_password'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.dashboard'));
            } catch (Exception $e) {
                $this->notificationHandler->error($e->getMessage());
            }
        }

        return $this->render('@EzPlatformAdminUi/user-profile/change_user_password.html.twig', [
            'form_change_user_password' => $form->createView(),
        ]);
    }
}
