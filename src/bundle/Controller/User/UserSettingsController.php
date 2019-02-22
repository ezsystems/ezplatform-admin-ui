<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\User;

use EzSystems\EzPlatformUser\View\UserSettings\ListView;
use EzSystems\EzPlatformUserBundle\Controller\UserSettingsController as BaseUserSettingsController;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Deprecated in 1.5 and will be removed in 2.0. Please use \EzSystems\EzPlatformUserBundle\Controller\UserSettingsController instead.
 */
class UserSettingsController extends Controller
{
    /** @var \EzSystems\EzPlatformUserBundle\Controller\UserSettingsController */
    private $userSettingsController;

    /**
     * @param \EzSystems\EzPlatformUserBundle\Controller\UserSettingsController $userSettingsController
     */
    public function __construct(BaseUserSettingsController $userSettingsController)
    {
        $this->userSettingsController = $userSettingsController;
    }

    /**
     * @param int $page
     *
     * @return \EzSystems\EzPlatformUser\View\UserSettings\ListView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function listAction(int $page = 1): ListView
    {
        return $this->userSettingsController->listAction($page);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $identifier
     *
     * @return \EzSystems\EzPlatformUser\View\UserSettings\UpdateView|null|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function updateAction(Request $request, string $identifier)
    {
        return $this->userSettingsController->updateAction($request, $identifier);
    }
}
