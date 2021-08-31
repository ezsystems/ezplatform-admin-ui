<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcher;
use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register FieldType form mappers in the mapper dispatcher.
 */
class FieldTypeFormMapperDispatcherPass implements CompilerPassInterface
{
    public const FIELD_TYPE_FORM_MAPPER_DISPATCHER = FieldTypeDefinitionFormMapperDispatcher::class;
    public const DEPRECATED_FIELD_TYPE_FORM_MAPPER_DEFINITION_SERVICE_TAG = 'ez.fieldFormMapper.definition';
    public const FIELD_TYPE_FORM_MAPPER_DEFINITION_SERVICE_TAG = 'ezplatform.field_type.form_mapper.definition';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::FIELD_TYPE_FORM_MAPPER_DISPATCHER)) {
            return;
        }

        $dispatcherDefinition = $container->findDefinition(self::FIELD_TYPE_FORM_MAPPER_DISPATCHER);

        foreach ($this->findTaggedFormMapperServices($container) as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['fieldType'])) {
                    throw new LogicException(
                        '`ezplatform.field_type.form_mapper` or deprecated `ez.fieldFormMapper` service tags need a "fieldType" attribute to identify which Field Type the mapper is for.'
                    );
                }

                $dispatcherDefinition->addMethodCall('addMapper', [new Reference($id), $tag['fieldType']]);
            }
        }
    }

    /**
     * Gathers services tagged as either
     * - ez.fieldFormMapper.value (deprecated)
     * - ez.fieldFormMapper.definition (deprecated)
     * - ezplatform.field_type.form_mapper.value
     * - ezplatform.field_type.form_mapper.definition.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return array
     */
    private function findTaggedFormMapperServices(ContainerBuilder $container): array
    {
        $deprecatedFieldFormMapperDefinitionTags = $container->findTaggedServiceIds(self::DEPRECATED_FIELD_TYPE_FORM_MAPPER_DEFINITION_SERVICE_TAG);
        $fieldFormMapperDefinitionTags = $container->findTaggedServiceIds(self::FIELD_TYPE_FORM_MAPPER_DEFINITION_SERVICE_TAG);

        foreach ($deprecatedFieldFormMapperDefinitionTags as $ezFieldFormMapperValueTag) {
            @trigger_error(
                sprintf(
                    'The `%s` service tag is deprecated and will be removed in eZ Platform 4.0. Use `%s` instead.',
                    self::DEPRECATED_FIELD_TYPE_FORM_MAPPER_DEFINITION_SERVICE_TAG,
                    self::FIELD_TYPE_FORM_MAPPER_DEFINITION_SERVICE_TAG
                ),
                E_USER_DEPRECATED
            );
        }

        return array_merge(
            $deprecatedFieldFormMapperDefinitionTags,
            $fieldFormMapperDefinitionTags
        );
    }
}
