<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformUser\UserSetting\UserSettingArrayAccessor;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @internal
 *
 * @todo should be moved to ezplatform-user
 */
class UserPreferencesGlobalExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingArrayAccessor */
    protected $userSettingArrayAccessor;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\UserSettingArrayAccessor $userSettingArrayAccessor
     */
    public function __construct(
        UserSettingArrayAccessor $userSettingArrayAccessor
    ) {
        $this->userSettingArrayAccessor = $userSettingArrayAccessor;
    }

    /**
     * @return array
     */
    public function getGlobals(): array
    {
        // has to use \ArrayAccess object due to BC promise

        return [
            'ez_user_settings' => $this->userSettingArrayAccessor,
        ];
    }
}
