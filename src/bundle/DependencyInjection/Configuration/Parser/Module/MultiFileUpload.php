<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Module;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class MultiFileUpload extends AbstractParser
{
    /**
     * {@inheritdoc}
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
          ->arrayNode('multifile_upload_module')
                ->validate()
                    ->always(function ($v) {
                        if (empty($v['location_mappings'])) {
                            unset($v['location_mappings']);
                        }

                        if (empty($v['default_mappings'])) {
                            unset($v['default_mappings']);
                        }

                        return $v;
                    })
                ->end()
                ->children()
                    ->arrayNode('location_mappings')
                        ->info('Let\'s you assign mappings bound to a location')
                        ->example([
                            [
                                'content_type_identifier' => 'gallery',
                                'mime_type_filter' => [
                                    'image/*',
                                ],
                                'mappings' => [
                                    [
                                        'mime_types' => [
                                            'image/jpeg',
                                            'image/png',
                                        ],
                                        'content_type_identifier' => 'image',
                                        'content_field_identifier' => 'image',
                                        'name_field_identifier' => 'name',
                                    ],
                                ],
                            ],
                        ])
                        ->prototype('array')
                            ->children()
                                ->scalarNode('content_type_identifier')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('mime_type_filter')
                                    ->defaultValue([])
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('mappings')
                                    ->info('Configure mappings between mime-type and content type identifier')
                                    ->isRequired()
                                    ->requiresAtLeastOneElement()
                                    ->prototype('array')
                                        ->children()
                                            ->arrayNode('mime_types')
                                                ->isRequired()
                                                ->requiresAtLeastOneElement()
                                                ->prototype('scalar')->end()
                                            ->end()
                                            ->scalarNode('content_type_identifier')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                            ->scalarNode('content_field_identifier')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                            ->scalarNode('name_field_identifier')
                                                ->isRequired()
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('default_mappings')
                        ->info('These mappings are used as a fallback in case there are no entries under `locations` key')
                        ->prototype('array')
                            ->children()
                                ->arrayNode('mime_types')
                                    ->isRequired()
                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')->end()
                                ->end()
                                ->scalarNode('content_type_identifier')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('content_field_identifier')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('name_field_identifier')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('fallback_content_type')
                        ->info('This content type will be used for files with no mime type mapping')
                        ->children()
                            ->scalarNode('content_type_identifier')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('content_field_identifier')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('name_field_identifier')
                                ->defaultNull()
                            ->end()
                        ->end()
                    ->end()
                    ->integerNode('max_file_size')
                        ->defaultValue(64000000) // 64MB
                    ->end()
                ->end()
          ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings['multifile_upload_module'])) {
            return;
        }

        $configurationParameters = [
            'location_mappings',
            'default_mappings',
            'fallback_content_type',
            'max_file_size',
        ];

        foreach ($configurationParameters as $parameter) {
            $contextualizer->setContextualParameter(
                'multifile_upload_module.' . $parameter,
                $currentScope,
                $scopeSettings['multifile_upload_module'][$parameter]
            );
        }
    }
}
