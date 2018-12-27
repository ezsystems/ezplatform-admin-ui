<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * {@inheritdoc}
 */
class TabPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG_TAB = 'ezplatform.tab';

    /**
     * @param ContainerBuilder $container
     *
     * @throws Exception\InvalidArgumentException When a tag is missing 'group' attribute.
     * @throws InvalidArgumentException In ContainerBuilder::findTaggedServiceIds() when a service is abstract.
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(TabRegistry::class)) {
            return;
        }

        $tabRegistryDefinition = $container->getDefinition(TabRegistry::class);
        $services = $this->findAndSortTaggedServices(static::TAG_TAB, $container);

        foreach ($services as $serviceReference) {
            $id = (string)$serviceReference;
            $definition = $container->getDefinition($id);
            $tags = $definition->getTag(static::TAG_TAB);

            foreach ($tags as $tag) {
                if (!isset($tag['group'])) {
                    throw new InvalidArgumentException($taggedServiceId, 'Tag ' . self::TAG_NAME . ' must contain "group" argument.');
                }
                $tabRegistryDefinition->addMethodCall('addTab', [$serviceReference, $tag['group']]);
            }
        }
    }
}
