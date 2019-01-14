<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

class ContentTypeIcons implements ProviderInterface
{
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
     * {@inheritdoc}
     */
    public function getConfig(): array
    {
        $config = [];

        $contentTypes = $this->configResolver->getParameter('content_type');
        foreach ($contentTypes as $identifier => $contentType) {
            $config[$identifier] = $contentType['thumbnail'];
        }

        return $config;
    }
}
