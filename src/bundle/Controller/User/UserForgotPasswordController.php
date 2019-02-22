<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\User;

use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use EzSystems\EzPlatformUserBundle\Controller\PasswordResetController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Deprecated in 1.5 and will be removed in 2.0. Please use \EzSystems\EzPlatformUserBundle\Controller\PasswordResetController instead.
 */
class UserForgotPasswordController extends Controller
{
    /** @var \EzSystems\EzPlatformUserBundle\Controller\PasswordResetController */
    private $passwordResetController;

    /**
     * @param \EzSystems\EzPlatformUserBundle\Controller\PasswordResetController $passwordResetController
     */
    public function __construct(
        PasswordResetController $passwordResetController
    ) {
        $this->passwordResetController = $passwordResetController;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformUser\View\ForgotPassword\FormView|\EzSystems\EzPlatformUser\View\ForgotPassword\SuccessView|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userForgotPasswordAction(Request $request)
    {
        return $this->passwordResetController->userForgotPasswordAction($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformUser\View\ForgotPassword\LoginView|\EzSystems\EzPlatformUser\View\ForgotPassword\SuccessView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userForgotPasswordLoginAction(Request $request)
    {
        return $this->passwordResetController->userForgotPasswordLoginAction($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $hashKey
     *
     * @return \EzSystems\EzPlatformUser\View\ResetPassword\FormView|\EzSystems\EzPlatformUser\View\ResetPassword\InvalidLinkView|\EzSystems\EzPlatformUser\View\ResetPassword\SuccessView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userResetPasswordAction(Request $request, string $hashKey)
    {
        return $this->passwordResetController->userResetPasswordAction($request, $hashKey);
    }
}
