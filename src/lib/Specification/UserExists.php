<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;

class UserExists implements UserSpecification
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /**
     * @param \eZ\Publish\API\Repository\UserService $userService
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
