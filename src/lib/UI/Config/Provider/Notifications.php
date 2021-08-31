<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Provides information about notifications.
 */
class Notifications implements ProviderInterface
{
    public const NOTIFICATION_TYPES = ['error', 'warning', 'info', 'success'];

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = [];
        foreach (self::NOTIFICATION_TYPES as $type) {
            $config[$type] = [
                'timeout' => $this->configResolver->getParameter(sprintf('notifications.%s.timeout', $type)),
            ];
        }

        return $config;
    }
}
