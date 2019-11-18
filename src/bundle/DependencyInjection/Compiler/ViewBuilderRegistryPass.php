<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\View\Builder\ContentTranslateViewBuilder;
use EzSystems\EzPlatformAdminUi\View\Builder\RelationViewBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to add View Builders to ViewBuilderRegistry.
 */
class ViewBuilderRegistryPass implements CompilerPassInterface
{
    public const VIEW_BUILDER_REGISTRY = 'ezpublish.view_builder.registry';
    public const VIEW_BUILDER_CONTENT_TRANSLATE = ContentTranslateViewBuilder::class;
    public const VIEW_BUILDER_RELATION = RelationViewBuilder::class;

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::VIEW_BUILDER_REGISTRY)) {
            return;
        }

        $viewBuilders = [];
        if ($container->hasDefinition(self::VIEW_BUILDER_CONTENT_TRANSLATE)) {
            $viewBuilders[] = $container->getDefinition(self::VIEW_BUILDER_CONTENT_TRANSLATE);
        }
        if ($container->hasDefinition(self::VIEW_BUILDER_RELATION)) {
            $viewBuilders[] = $container->getDefinition(self::VIEW_BUILDER_RELATION);
        }

        if ($viewBuilders) {
            $registry = $container->findDefinition(self::VIEW_BUILDER_REGISTRY);
            $registry->addMethodCall('addToRegistry', [$viewBuilders]);
        }
    }
}
