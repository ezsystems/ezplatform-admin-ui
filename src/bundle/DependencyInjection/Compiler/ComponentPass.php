<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\Component\Registry;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception;

class ComponentPass implements CompilerPassInterface
{
    public const TAG_NAME = 'ezplatform.admin_ui.component';

    /**
     * @param ContainerBuilder $container
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\ServiceNotFoundException
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(Registry::class)) {
            return;
        }

        $registryDefinition = $container->getDefinition(Registry::class);
        $taggedServiceIds = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($taggedServiceIds as $taggedServiceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['group'])) {
                    throw new InvalidArgumentException($taggedServiceId, 'Tag ' . self::TAG_NAME . ' must contain "group" argument.');
                }

                $registryDefinition->addMethodCall('addComponent', [$tag['group'], $taggedServiceId, new Reference($taggedServiceId)]);
            }
        }
    }
}
