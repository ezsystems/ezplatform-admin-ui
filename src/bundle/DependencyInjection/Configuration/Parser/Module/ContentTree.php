<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Module;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for Subitems module.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          content_tree_module:
 *              load_more_limit: 30
 *              children_load_max_limit: 200
 *              tree_max_depth: 10
 *              tree_root_location_id: ~ # use tree root location from SA
 *              allowed_content_types: '*'
 *              ignored_content_types: [article, post]
 * ```
 */
class ContentTree extends AbstractParser
{
    /**
     * @inheritdoc
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('content_tree_module')
                ->info('Content Tree module configuration')
                ->children()
                    ->integerNode('load_more_limit')
                        ->info('Number of children to load in expand and load more operations')
                        ->defaultValue(30)
                        ->min(1)
                    ->end()
                    ->integerNode('children_load_max_limit')
                        ->info('Total limit of loaded children in single node')
                        ->defaultValue(200)
                        ->min(1)
                    ->end()
                    ->integerNode('tree_max_depth')
                        ->info('Max depth of expanded tree')
                        ->defaultValue(10)
                        ->min(1)
                    ->end()
                    ->integerNode('tree_root_location_id')
                        ->info(
                            'Location that will be used as a tree root. User won\'t be able to see ancestors of this location.' . "\n\n"
                            . 'When set to \'null\' SiteAccess \'content.tree_root.location_id\' setting will be used.'
                        )
                        ->defaultValue(null)
                    ->end()
                    ->arrayNode('contextual_tree_root_location_ids')
                        ->info(
                            'List of Location IDs overriding \'tree_root_location_id\' setting.' . "\n\n"
                            . 'Tree Root is overriden when previewing those Locations in AdminUI.'
                        )
                        ->example([
                            '2 # Home',
                            '5 # Users',
                            '43 # Media',
                        ])
                        ->defaultValue([])
                        ->integerPrototype()->end()
                    ->end()
                    ->arrayNode('allowed_content_types')
                        ->beforeNormalization()
                            ->ifString()
                            ->thenEmptyArray()
                        ->end()
                        ->info(
                            'List of content type identifiers to show in Content Tree. ' . "\n\n"
                            . 'Use string value \'*\' to display all content types. '
                            . 'Empty array also means all content types will be displayed.'
                        )
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('ignored_content_types')
                        ->info(
                            'List of content type identifiers to ignore in Content Tree.' . "\n\n"
                            . 'This option is only useful when used with \'allowed_content_types = *\'.'
                        )
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @inheritdoc
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['content_tree_module'])) {
            return;
        }

        $settings = $scopeSettings['content_tree_module'];

        if (array_key_exists('load_more_limit', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.load_more_limit',
                $currentScope,
                $settings['load_more_limit']
            );
        }

        if (array_key_exists('children_load_max_limit', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.children_load_max_limit',
                $currentScope,
                $settings['children_load_max_limit']
            );
        }

        if (array_key_exists('tree_max_depth', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.tree_max_depth',
                $currentScope,
                $settings['tree_max_depth']
            );
        }

        if (array_key_exists('tree_root_location_id', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.tree_root_location_id',
                $currentScope,
                $settings['tree_root_location_id']
            );
        }

        if (array_key_exists('contextual_tree_root_location_ids', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.contextual_tree_root_location_ids',
                $currentScope,
                $settings['contextual_tree_root_location_ids']
            );
        }

        if (array_key_exists('allowed_content_types', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.allowed_content_types',
                $currentScope,
                $settings['allowed_content_types']
            );
        }

        if (array_key_exists('ignored_content_types', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.ignored_content_types',
                $currentScope,
                $settings['ignored_content_types']
            );
        }
    }
}
