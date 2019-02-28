<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting;

use EzSystems\EzPlatformUser\UserSetting\UserSetting;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService as BaseUserSettingService;

/**
 * @internal
 *
 * @deprecated Deprecated in 1.5 and will be removed in 2.0. Please use \EzSystems\EzPlatformUser\UserSetting\UserSettingService instead.
 */
class UserSettingService
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    private $userSettingService;

    public function __construct(
        BaseUserSettingService $userSettingService
    ) {
        $this->userSettingService = $userSettingService;
    }

    /**
     * @param string $identifier
     * @param string $value
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function setUserSetting(string $identifier, string $value): void
    {
        $this->userSettingService->setUserSetting($identifier, $value);
    }

    /**
     * @param string $identifier
     *
     * @return \EzSystems\EzPlatformUser\UserSetting\UserSetting
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getUserSetting(string $identifier): UserSetting
    {
        return $this->userSettingService->getUserSetting($identifier);
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function loadUserSettings(int $offset = 0, int $limit = 25): array
    {
        return $this->userSettingService->loadUserSettings($offset, $limit);
    }

    /**
     * @return int
     */
    public function countUserSettings(): int
    {
        return $this->userSettingService->countUserSettings();
    }
}
