<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\Component\Registry;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ComponentPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG_NAME = 'ezplatform.admin_ui.component';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException When a service is abstract
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException When a tag is missing 'group' attribute
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(Registry::class)) {
            return;
        }

        $registryDefinition = $container->getDefinition(Registry::class);
        $services = $this->findAndSortTaggedServices(self::TAG_NAME, $container);

        foreach ($services as $serviceReference) {
            $id = (string)$serviceReference;
            $definition = $container->getDefinition($id);
            $tags = $definition->getTag(static::TAG_NAME);

            foreach ($tags as $tag) {
                if (!isset($tag['group'])) {
                    throw new InvalidArgumentException($id, 'Tag ' . self::TAG_NAME . ' must contain a "group" argument.');
                }
                $registryDefinition->addMethodCall('addComponent', [$tag['group'], $id, $serviceReference]);
            }
        }
    }
}
