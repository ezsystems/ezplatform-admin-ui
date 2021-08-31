<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Notification;

/**
 * Generates user notifications with translatable message.
 */
interface TranslatableNotificationHandlerInterface
{
    public function info(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): void;

    public function success(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): void;

    public function warning(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): void;

    public function error(string $message, array $parameters = [], ?string $domain = null, ?string $locale = null): void;
}
