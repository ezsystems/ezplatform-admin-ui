<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\AdminUi\Templating\Twig;

use Ibexa\AdminUi\Component\Registry as ComponentRegistry;
use Ibexa\Contracts\AdminUi\Component\Renderer\RendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ComponentExtension extends AbstractExtension
{
    protected $registry;

    protected $renderer;

    public function __construct(
        ComponentRegistry $registry,
        RendererInterface $renderer
    ) {
        $this->registry = $registry;
        $this->renderer = $renderer;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_render_component_group',
                [$this, 'renderComponentGroup'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'ez_render_component',
                [$this, 'renderComponent'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function renderComponentGroup(string $group, array $parameters = [])
    {
        return implode('', $this->renderer->renderGroup($group, $parameters));
    }

    public function renderComponent(string $group, string $id, array $parameters = [])
    {
        return $this->renderer->renderSingle($group, $id, $parameters);
    }
}

class_alias(ComponentExtension::class, 'EzSystems\EzPlatformAdminUiBundle\Templating\Twig\ComponentExtension');
