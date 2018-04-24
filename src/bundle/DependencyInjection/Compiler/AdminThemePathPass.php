<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass which maps Resources/views directory to admin Theme and Design of eZ Design Engine.
 */
class AdminThemePathPass implements CompilerPassInterface
{
    /**
     * Append Resources/views to the list of paths for admin Theme.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $templatesPathMap = $container->hasParameter('ezdesign.templates_path_map')
            ? $container->getParameter('ezdesign.templates_path_map')
            : [];

        $templatesPathMap['admin'][] = realpath(__DIR__ . '/../../Resources/views');

        $container->setParameter('ezdesign.templates_path_map', $templatesPathMap);
    }
}
