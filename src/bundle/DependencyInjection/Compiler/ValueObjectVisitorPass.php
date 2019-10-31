<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ValueObjectVisitorPass implements CompilerPassInterface
{
    private const TAG_NAME = 'ezplatform.admin_ui.rest.value_object_visitor';
    private const DISPATCHER_DEFINITION_ID = 'ezplatform.adminui.rest.output.value_object_visitor.dispatcher';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::DISPATCHER_DEFINITION_ID)) {
            return;
        }

        $definition = $container->getDefinition(self::DISPATCHER_DEFINITION_ID);

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'])) {
                    throw new \LogicException(self::TAG_NAME . ' service tag needs a "type" attribute to identify the field type. None given.');
                }

                $definition->addMethodCall(
                    'addVisitor',
                    [$attribute['type'], new Reference($id)]
                );
            }
        }
    }
}
