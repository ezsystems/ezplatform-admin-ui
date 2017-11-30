<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformAdminUi\Component\Registry as ComponentRegistry;
use EzSystems\EzPlatformAdminUi\Component\Renderable;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use Twig_Extension;
use Twig_SimpleFunction;

class ComponentExtension extends Twig_Extension
{
    protected $registry;

    public function __construct(
        ComponentRegistry $registry
    ) {
        $this->registry = $registry;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'ezplatform_admin_ui_component_group',
                [$this, 'renderComponentGroup'],
                ['is_safe' => ['html']]
            ),
            new Twig_SimpleFunction(
                'ezplatform_admin_ui_component',
                [$this, 'renderComponent'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function renderComponentGroup(string $group, array $parameters = [])
    {
        $components = $this->registry->getComponents($group);
        $outputs = array_map(function (Renderable $component) use ($parameters) {
            return $component->render($parameters);
        }, $components);

        return implode('', $outputs);
    }

    public function renderComponent(string $group, string $id, array $parameters = [])
    {
        $components = $this->registry->getComponents($group);

        if (!isset($components[$id])) {
            throw new InvalidArgumentException('id', sprintf("Can't find Component '%s' in group '%s'", $id, $group));
        }

        /** @var Renderable $component */
        $component = $components[$id];

        return $component->render($parameters);
    }
}
