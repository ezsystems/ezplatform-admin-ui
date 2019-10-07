<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use LogicException;

/**
 * Compiler pass to register Limitation value mappers.
 */
class LimitationValueMapperPass implements CompilerPassInterface
{
    const LIMITATION_VALUE_MAPPER_REGISTRY = 'ezrepoforms.limitation_value_mapper.registry';
    const LIMITATION_VALUE_MAPPER_TAG = 'ez.limitation.valueMapper';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::LIMITATION_VALUE_MAPPER_REGISTRY)) {
            return;
        }

        $registry = $container->findDefinition(self::LIMITATION_VALUE_MAPPER_REGISTRY);

        foreach ($container->findTaggedServiceIds(self::LIMITATION_VALUE_MAPPER_TAG) as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['limitationType'])) {
                    throw new LogicException(sprintf(
                        '%s service tag needs a "limitationType" attribute to identify which LimitationType the mapper is for. None given.',
                        self::LIMITATION_VALUE_MAPPER_TAG
                    ));
                }

                $registry->addMethodCall('addMapper', [new Reference($id), $attribute['limitationType']]);
            }
        }
    }
}
