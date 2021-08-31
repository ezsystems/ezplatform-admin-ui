<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Service;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\Asset\Packages;

final class ContentTypeIconResolver
{
    private const DEFAULT_IDENTIFIER = 'default-config';
    private const PARAM_NAME_FORMAT = 'content_type.%s';

    private const ICON_KEY = 'thumbnail';

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\Asset\Packages */
    private $packages;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \Symfony\Component\Asset\Packages $packages
     */
    public function __construct(ConfigResolverInterface $configResolver, Packages $packages)
    {
        $this->configResolver = $configResolver;
        $this->packages = $packages;
    }

    /**
     * Returns path to content type icon.
     *
     * Path is resolved based on configuration (ezpublish.system.<SCOPE>.content_type.<IDENTIFIER>). If there isn't
     * corresponding entry for given content type, then path to default icon will be returned.
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\ContentTypeIconNotFoundException
     */
    public function getContentTypeIcon(string $identifier): string
    {
        $icon = $this->resolveIcon($identifier);

        $fragment = null;
        if (strpos($icon, '#') !== false) {
            list($icon, $fragment) = explode('#', $icon);
        }

        return $this->packages->getUrl($icon) . ($fragment ? '#' . $fragment : '');
    }

    /**
     * @throws \EzSystems\EzPlatformAdminUi\Exception\ContentTypeIconNotFoundException
     */
    private function resolveIcon(string $identifier): string
    {
        $parameterName = $this->getConfigParameterName($identifier);
        $defaultParameterName = $this->getConfigParameterName(self::DEFAULT_IDENTIFIER);

        if ($this->configResolver->hasParameter($parameterName)) {
            $config = $this->configResolver->getParameter($parameterName);
        }

        if ((empty($config) || empty($config[self::ICON_KEY])) && $this->configResolver->hasParameter($defaultParameterName)) {
            $config = $this->configResolver->getParameter($defaultParameterName);
        }

        return $config[self::ICON_KEY] ?? '';
    }

    /**
     * Return configuration parameter name for given content type identifier.
     */
    private function getConfigParameterName(string $identifier): string
    {
        return sprintf(self::PARAM_NAME_FORMAT, $identifier);
    }
}
