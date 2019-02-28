<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\UserProfile;

use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use EzSystems\EzPlatformUserBundle\Controller\PasswordChangeController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Deprecated in 1.5 and will be removed in 2.0. Please use \EzSystems\EzPlatformUserBundle\Controller\PasswordChangeController instead.
 */
class UserPasswordChangeController extends Controller
{
    /** @var \EzSystems\EzPlatformUserBundle\Controller\PasswordChangeController */
    private $passwordChangeController;

    /**
     * @param \EzSystems\EzPlatformUserBundle\Controller\PasswordChangeController $passwordChangeController
     */
    public function __construct(PasswordChangeController $passwordChangeController)
    {
        $this->passwordChangeController = $passwordChangeController;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformUser\View\ForgotPassword\ChangePassword\FormView|\EzSystems\EzPlatformUser\View\ForgotPassword\ChangePassword\SuccessView|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userPasswordChangeAction(Request $request)
    {
        return $this->passwordChangeController->userPasswordChangeAction($request);
    }
}
