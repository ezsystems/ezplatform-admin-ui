<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\User;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\UserTokenUpdateStruct;
use Swift_Mailer;
use Twig_Environment;
use DateTime;
use DateInterval;
use Swift_Message;

class UserForgotPasswordController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var Swift_Mailer */
    private $mailer;

    /** @var Twig_Environment */
    private $twig;

    /** @var string */
    private $tokenIntervalSpec;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param Swift_Mailer $mailer
     * @param Twig_Environment $twig
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param string $tokenIntervalSpec
     */
    public function __construct(
        FormFactory $formFactory,
        UserService $userService,
        Swift_Mailer $mailer,
        Twig_Environment $twig,
        NotificationHandlerInterface $notificationHandler,
        PermissionResolver $permissionResolver,
        string $tokenIntervalSpec
    ) {
        $this->formFactory = $formFactory;
        $this->userService = $userService;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->notificationHandler = $notificationHandler;
        $this->permissionResolver = $permissionResolver;
        $this->tokenIntervalSpec = $tokenIntervalSpec;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Exception
     */
    public function userForgotPasswordAction(Request $request): Response
    {
        $form = $this->formFactory->forgotUserPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $users = $this->userService->loadUsersByEmail($data->getEmail());

            /** Because is is possible to have multiple user accounts with same email address we must gain a user login. */
            if (count($users) > 1) {
                return $this->redirectToRoute('ezplatform.user.forgot_password.login');
            }

            if (!empty($users)) {
                $user = reset($users);
                $token = $this->updateUserToken($user);

                $this->sendResetPasswordMessage($user->email, $token);
            }

            return $this->render('@ezdesign/Security/forgot_user_password/success.html.twig');
        }

        return $this->render('@ezdesign/Security/forgot_user_password/index.html.twig', [
            'form_forgot_user_password' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Exception
     */
    public function userForgotPasswordLoginAction(Request $request): Response
    {
        $form = $this->formFactory->forgotUserPasswordWithLogin();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $user = $this->userService->loadUserByLogin($data->getLogin());
            } catch (NotFoundException $e) {
                $user = null;
            }

            if (!$user || count($this->userService->loadUsersByEmail($user->email)) < 2) {
                return $this->render('@ezdesign/Security/forgot_user_password/success.html.twig');
            }

            $token = $this->updateUserToken($user);
            $this->sendResetPasswordMessage($user->email, $token);

            return $this->render('@ezdesign/Security/forgot_user_password/success.html.twig');
        }

        return $this->render('@ezdesign/Security/forgot_user_password/with_login.html.twig', [
            'form_forgot_user_password_with_login' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $hashKey
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function userResetPasswordAction(Request $request, string $hashKey): Response
    {
        $response = new Response();
        $response->headers->set('X-Robots-Tag', 'noindex');

        try {
            $user = $this->userService->loadUserByToken($hashKey);
        } catch (NotFoundException $e) {
            return $this->render('@ezdesign/Security/reset_user_password/invalid_link.html.twig', [], $response);
        }

        $form = $this->formFactory->resetUserPassword(null, null, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $currentUser = $this->permissionResolver->getCurrentUserReference();

                $this->permissionResolver->setCurrentUserReference($user);

                $userUpdateStruct = $this->userService->newUserUpdateStruct();
                $userUpdateStruct->password = $data->getNewPassword();

                $this->userService->updateUser($user, $userUpdateStruct);
                $this->userService->expireUserToken($hashKey);

                $this->permissionResolver->setCurrentUserReference($currentUser);

                return $this->render('@ezdesign/Security/reset_user_password/success.html.twig', [], $response);
            } catch (\Exception $e) {
                $this->notificationHandler->error($e->getMessage());
            }
        }

        return $this->render('@ezdesign/Security/reset_user_password/index.html.twig', [
            'form_reset_user_password' => $form->createView(),
        ], $response);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User $user
     *
     * @return string
     *
     * @throws \Exception
     */
    private function updateUserToken(User $user): string
    {
        $struct = new UserTokenUpdateStruct();
        $struct->hashKey = bin2hex(random_bytes(16));
        $date = new DateTime();
        $date->add(new DateInterval($this->tokenIntervalSpec));
        $struct->time = $date;
        $this->userService->updateUserToken($user, $struct);

        return $struct->hashKey;
    }

    /**
     * @param string $to
     * @param string $hashKey
     *
     * @throws \Twig_Error_Syntax
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Loader
     */
    private function sendResetPasswordMessage(string $to, string $hashKey)
    {
        $template = $this->twig->loadTemplate('@ezdesign/Security/mail/forgot_user_password.html.twig');

        $subject = $template->renderBlock('subject', []);
        $from = $template->renderBlock('from', []);
        $body = $template->renderBlock('body', ['hashKey' => $hashKey]);

        $message = (new Swift_Message())
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
