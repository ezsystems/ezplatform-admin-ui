<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\User;

use EzSystems\EzPlatformUserBundle\Controller\UserSettingsController as BaseUserSettingsController;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Deprecated in 1.5 and will be removed in 2.0. Please use \EzSystems\EzPlatformUserBundle\Controller\UserSettingsController instead.
 */
class UserSettingsController extends Controller
{
    /**
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function listAction(int $page = 1)
    {
        return $this->forward(BaseUserSettingsController::class . '::listAction', [
            'page' => $page,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, string $identifier)
    {
        return $this->forward(BaseUserSettingsController::class . '::updateAction', [
            'identifier' => $identifier,
        ]);
    }
}
