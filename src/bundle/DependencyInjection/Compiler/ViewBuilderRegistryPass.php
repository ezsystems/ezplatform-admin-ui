<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\View\Builder\ContentTranslateViewBuilder;
use EzSystems\EzPlatformAdminUi\View\Builder\ContentCreateViewBuilder;
use EzSystems\EzPlatformAdminUi\View\Builder\ContentEditViewBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to add View Builders to ViewBuilderRegistry.
 */
class ViewBuilderRegistryPass implements CompilerPassInterface
{
    public const VIEW_BUILDER_REGISTRY = 'ezpublish.view_builder.registry';
    public const VIEW_BUILDER_CONTENT_TRANSLATE = ContentTranslateViewBuilder::class;
    public const VIEW_BUILDER_CONTENT_EDIT = ContentEditViewBuilder::class;
    public const VIEW_BUILDER_CONTENT_CREATE = ContentCreateViewBuilder::class;
    public const VIEW_BUILDERS = [
        self::VIEW_BUILDER_CONTENT_TRANSLATE,
        self::VIEW_BUILDER_CONTENT_EDIT,
        self::VIEW_BUILDER_CONTENT_CREATE,
    ];

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function process(ContainerBuilder $container)
    {
        if (
            !$container->hasDefinition(self::VIEW_BUILDER_REGISTRY)
        ) {
            return;
        }

        $registry = $container->findDefinition(self::VIEW_BUILDER_REGISTRY);
        $viewBuilders = [];

        foreach (self::VIEW_BUILDERS as $viewBuilder) {
            if (!$container->hasDefinition($viewBuilder)) {
                continue;
            }

            $viewBuilders[] = $container->getDefinition($viewBuilder);
        }

        $registry->addMethodCall('addToRegistry', [$viewBuilders]);
    }
}
