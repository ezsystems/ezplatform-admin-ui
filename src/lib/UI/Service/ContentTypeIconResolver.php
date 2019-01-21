<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Service;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\Asset\Packages;

class ContentTypeIconResolver
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\Asset\Packages */
    private $packages;

    /** @var string|null */
    private $defaultThumbnail;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \Symfony\Component\Asset\Packages $packages
     * @param string|null $defaultThumbnail
     */
    public function __construct(ConfigResolverInterface $configResolver, Packages $packages, string $defaultThumbnail)
    {
        $this->configResolver = $configResolver;
        $this->packages = $packages;
        $this->defaultThumbnail = $defaultThumbnail;
    }

    /**
     * Returns path to content type icon.
     *
     * Path is resolved based on configuration (ezpublish.system.<SCOPE>.content_type). If there isn't coresponding
     * entry for given content type, then path to default icon will be returned.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|string $contentType
     *
     * @return string|null
     */
    public function getContentTypeIcon($contentType): ?string
    {
        if ($contentType instanceof ContentType) {
            $contentType = $contentType->identifier;
        }

        $thumbnail = null;

        $parameterName = $this->getConfigParameterName($contentType);
        if ($this->configResolver->hasParameter($parameterName)) {
            $thumbnail = $this->configResolver->getParameter($parameterName)['thumbnail'];
        }

        if (empty($thumbnail)) {
            $thumbnail = $this->defaultThumbnail;
        }

        $fragment = null;
        if (strpos($thumbnail, '#') !== false) {
            list($thumbnail, $fragment) = explode('#', $thumbnail);
        }

        return $this->packages->getUrl($thumbnail) . ($fragment ? '#' . $fragment : '');
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
        return "content_type.$identifier";
    }
}
