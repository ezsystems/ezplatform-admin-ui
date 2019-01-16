<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Service;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class ContentTypeIconResolver
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var string|null */
    private $defaultThumbnail;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param string|null $defaultThumbnail
     */
    public function __construct(ConfigResolverInterface $configResolver, string $defaultThumbnail)
    {
        $this->configResolver = $configResolver;
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

        $config = $this->configResolver->getParameter('content_type');

        if (isset($config[$contentType]) && !empty($config[$contentType]['thumbnail'])) {
            return $config[$contentType]['thumbnail'];
        }

        return $this->defaultThumbnail;
    }
}
