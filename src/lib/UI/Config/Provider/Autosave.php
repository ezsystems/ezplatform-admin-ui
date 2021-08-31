<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use EzSystems\EzPlatformAdminUi\UserSetting\Autosave as AutosaveSetting;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;

class Autosave implements ProviderInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    private $userSettingService;

    public function __construct(
        UserSettingService $userSettingService
    ) {
        $this->userSettingService = $userSettingService;
    }

    public function getConfig(): array
    {
        $isEnabled = $this->userSettingService->getUserSetting('autosave')->value === AutosaveSetting::ENABLED_OPTION;
        $interval = (int)$this->userSettingService->getUserSetting('autosave_interval')->value * 1000;

        return [
            'enabled' => $isEnabled,
            'interval' => $interval,
        ];
    }
}
