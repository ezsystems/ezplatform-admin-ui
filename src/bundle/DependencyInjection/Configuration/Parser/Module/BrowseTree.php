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

/**
 * Configuration parser for Browse tree widget.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          browse_tree:
 *              pagination_children: 10
 *              exclude_content_types:
 *                  - user
 * ```
 */
class BrowseTree extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('browse_tree')
                ->info('Browse tree widget configuration')
                ->children()
                    ->scalarNode('pagination_children')
                        ->isRequired()
                        ->info('Default pagination for childre node')
                    ->end()
                    ->arrayNode('exclude_content_types')
                        ->info('Exclude content from tree menu by content type')
                        ->scalarPrototype()
                    ->end();
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['browse_tree'])) {
            return;
        }

        $settings = $scopeSettings['browse_tree'];

        if (!isset($settings['pagination_children'])) {
            return;
        }

        $contextualizer->setContextualParameter(
            'browse_tree.pagination_children',
            $currentScope,
            $settings['pagination_children']
        );

        $contextualizer->setContextualParameter(
            'browse_tree.exclude_content_types',
            $currentScope,
            $settings['exclude_content_types']
        );
    }
}