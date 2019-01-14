<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class ContentTypeIconExtension extends Twig_Extension
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var string|null */
    private $defaultThumbnail;

    public function __construct(ConfigResolverInterface $configResolver, string $defaultThumbnail = null)
    {
        $this->configResolver = $configResolver;
        $this->defaultThumbnail = $defaultThumbnail;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction(
                'ezplatform_admin_ui_content_type_icon',
                [$this, 'getContentTypeIcon'],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * Returns path to content type icon.
     *
     * Path is resolved based on configuration (ezpublish.system.<SCOPE>.content_type). If there isn't coresponding
     * entry for given content type, then path to default icon will be returned.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|string $contentType
     *
     * @return null|string
     */
    public function getContentTypeIcon($contentType): ?string
    {
        if ($contentType instanceof ContentType) {
            $contentType = $contentType->identifier;
        }

        $config = $this->configResolver->getParameter('content_type');
        if (isset($config[$contentType])) {
            return $config[$contentType]['thumbnail'];
        }

        return $this->defaultThumbnail;
    }
}
