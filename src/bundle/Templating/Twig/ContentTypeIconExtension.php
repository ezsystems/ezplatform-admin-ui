<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentTypeIconExtension extends AbstractExtension
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
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_content_type_icon',
                [$this->contentTypeIconResolver, 'getContentTypeIcon'],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }
}
