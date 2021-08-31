<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;

class DateFormat implements ProviderInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    protected $userSettingService;

    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer */
    protected $dateTimeFormatSerializer;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\UserSettingService $userSettingService
     * @param \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer $dateTimeFormatSerializer
     */
    public function __construct(UserSettingService $userSettingService, DateTimeFormatSerializer $dateTimeFormatSerializer)
    {
        $this->userSettingService = $userSettingService;
        $this->dateTimeFormatSerializer = $dateTimeFormatSerializer;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getConfig(): array
    {
        $fullDateTimeFormat = $this->dateTimeFormatSerializer->deserialize(
            $this->userSettingService->getUserSetting('full_datetime_format')->value
        );

        $shortDateTimeFormat = $this->dateTimeFormatSerializer->deserialize(
            $this->userSettingService->getUserSetting('short_datetime_format')->value
        );

        return [
            'fullDateTime' => (string)$fullDateTimeFormat,
            'fullDate' => $fullDateTimeFormat->getDateFormat(),
            'fullTime' => $fullDateTimeFormat->getTimeFormat(),
            'shortDateTime' => (string)$shortDateTimeFormat,
            'shortDate' => $shortDateTimeFormat->getDateFormat(),
            'shortTime' => $shortDateTimeFormat->getTimeFormat(),
        ];
    }
}
