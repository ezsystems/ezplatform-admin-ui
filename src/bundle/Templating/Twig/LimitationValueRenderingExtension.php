<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Limitation\Templating\LimitationBlockRendererInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LimitationValueRenderingExtension extends AbstractExtension
{
    /** @var \EzSystems\EzPlatformAdminUi\Limitation\Templating\LimitationBlockRenderer */
    private $limitationRenderer;

    public function __construct(LimitationBlockRendererInterface $limitationRenderer)
    {
        $this->limitationRenderer = $limitationRenderer;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_render_limitation_value',
                function (Environment $twig, Limitation $limitation, array $params = []) {
                    return $this->limitationRenderer->renderLimitationValue($limitation, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    public function getName(): string
    {
        return 'ezplatform.content_forms.limitation_value_rendering';
    }
}
