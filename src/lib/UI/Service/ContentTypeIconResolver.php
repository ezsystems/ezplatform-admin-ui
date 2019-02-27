<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
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
     * @param string $identifier
     *
     * @return string
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
     * @param string $identifier
     *
     * @return string
     */
    private function resolveIcon(string $identifier): string
    {
        $config = null;

        $parameterName = $this->getConfigParameterName($identifier);
        if ($this->configResolver->hasParameter($parameterName)) {
            $config = $this->configResolver->getParameter($parameterName);
        }

        if ($config === null || empty($config[self::ICON_KEY])) {
            $config = $this->configResolver->getParameter(
                $this->getConfigParameterName(self::DEFAULT_IDENTIFIER)
            );
        }

        return $config[self::ICON_KEY];
    }

    /**
     * Return configuration parameter name for given content type identifier.
     *
     * @param string $identifier
     *
     * @return string
     */
    private function getConfigParameterName(string $identifier): string
    {
        return sprintf(self::PARAM_NAME_FORMAT, $identifier);
    }
}
