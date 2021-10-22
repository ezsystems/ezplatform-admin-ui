<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\AdminUi\Notification;

interface NotificationHandlerInterface
{
    /**
     * @param string $message
     */
    public function info(string $message): void;

    /**
     * @param string $message
     */
    public function success(string $message): void;

    /**
     * @param string $message
     */
    public function warning(string $message): void;

    /**
     * @param string $message
     */
    public function error(string $message): void;
}

class_alias(NotificationHandlerInterface::class, 'EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface');
