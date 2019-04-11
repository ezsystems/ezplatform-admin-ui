<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Notification;

interface TranslatableNotificationHandlerInterface
{
    /**
     * @param string $message
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     */
    public function info(string $message, array $parameters, ?string $domain = null, ?string $locale = null): void;

    /**
     * @param string $message
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     */
    public function success(string $message, array $parameters, ?string $domain = null, ?string $locale = null): void;

    /**
     * @param string $message
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     */
    public function warning(string $message, array $parameters, ?string $domain = null, ?string $locale = null): void;

    /**
     * @param string $message
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     */
    public function error(string $message, array $parameters, ?string $domain = null, ?string $locale = null): void;
}
