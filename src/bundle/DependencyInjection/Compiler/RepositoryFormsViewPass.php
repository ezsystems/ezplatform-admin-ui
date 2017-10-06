<?php

namespace EzPlatformAdminUiBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * {@inheritDoc}
 */
class RepositoryFormsViewPass implements CompilerPassInterface
{
    const PAGELAYOUT_VIEW_PATH = 'EzPlatformAdminUiBundle::layout.html.twig';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezrepoforms.view_templates_listener')) {
            return;
        }

        $viewTemplatesListener = $container->getDefinition('ezrepoforms.view_templates_listener');
        $viewTemplatesListener->addMethodCall('setPagelayout', [self::PAGELAYOUT_VIEW_PATH]);
    }
}
