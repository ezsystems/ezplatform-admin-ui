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
 * @internal
 */
final class IconPaths implements ProviderInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function getConfig(): array
    {
        return [
            'iconSets' => $this->configResolver->getParameter('assets.icon_sets'),
            'defaultIconSet' => $this->configResolver->getParameter('assets.default_icon_set'),
        ];
    }
}
