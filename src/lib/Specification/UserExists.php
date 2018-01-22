<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

class UserExists implements UserSpecification
{
    /** @var UserService */
    private $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Checks if $userId is an existing User id.
     *
     * @param mixed $userId
     *
     * @return bool
     */
    public function isSatisfiedBy($userId): bool
    {
        try {
            $this->userService->loadUser($userId);

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }
}
