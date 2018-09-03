<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta;

use EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService;
use Pagerfanta\Adapter\AdapterInterface;

class UserSettingsAdapter implements AdapterInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService */
    private $userSettingService;

    /**
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService $userSettingService
     */
    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getNbResults(): int
    {
        return $this->userSettingService->countUserSettings();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getSlice($offset, $length): array
    {
        return $this->userSettingService->loadUserSettings($offset, $length);
    }
}
