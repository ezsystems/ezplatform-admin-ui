<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @internal
 *
 * @todo provide extensibility to map selected settings
 */
class UserPreferencesGlobalExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService */
    protected $userSettingService;

    /**
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService $userSettingService
     */
    public function __construct(
        UserSettingService $userSettingService
    ) {
        $this->userSettingService = $userSettingService;
    }

    /**
     * @return array
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getGlobals(): array
    {
        return [
            'ez_user_settings' => $this->getUserSettings(),
        ];
    }

    /**
     * @return array
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getUserSettings(): array
    {
        return [
            'timezone' => $this->getTimezoneValue(),
        ];
    }

    /**
     * @return string
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getTimezoneValue(): string
    {
        return $this->userSettingService->getUserSetting('timezone')->value;
    }
}
