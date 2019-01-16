<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver;
use Twig_Extension;
use Twig_SimpleFunction;

class ContentTypeIconExtension extends Twig_Extension
{
    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver */
    private $contentTypeIconResolver;

    /**
     * @param \EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver $contentTypeIconResolver
     */
    public function __construct(ContentTypeIconResolver $contentTypeIconResolver)
    {
        $this->contentTypeIconResolver = $contentTypeIconResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction(
                'ezplatform_admin_ui_content_type_icon',
                [$this->contentTypeIconResolver, 'getContentTypeIcon'],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }
}
