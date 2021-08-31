<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register Limitation form mappers.
 */
class LimitationFormMapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezplatform.content_forms.limitation_form_mapper.registry')) {
            return;
        }

        $registry = $container->findDefinition('ezplatform.content_forms.limitation_form_mapper.registry');

        foreach ($container->findTaggedServiceIds('ez.limitation.formMapper') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['limitationType'])) {
                    throw new LogicException(
                        'ez.limitation.formMapper service tag needs a "limitationType" attribute to identify which LimitationType the mapper is for.'
                    );
                }

                $registry->addMethodCall('addMapper', [new Reference($id), $attribute['limitationType']]);
            }
        }
    }
}
