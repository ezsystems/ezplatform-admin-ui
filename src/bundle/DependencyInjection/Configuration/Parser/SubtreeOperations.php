<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for subtree related operations.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          subtree_operations:
 *              copy_subtree:
 *                  limit: 200
 * ```
 */
class SubtreeOperations extends AbstractParser
{
    /**
     * @inheritdoc
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings['subtree_operations'])) {
            return;
        }

        $settings = $scopeSettings['subtree_operations'];
        $nodes = ['copy_subtree' => ['limit']];

        foreach ($nodes as $node => $keys) {
            foreach ($keys as $key) {
                if (!isset($settings[$node][$key]) || empty($settings[$node][$key])) {
                    continue;
                }

                $contextualizer->setContextualParameter(
                    sprintf('subtree_operations.%s.%s', $node, $key),
                    $currentScope,
                    $settings[$node][$key]
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('subtree_operations')
                ->info('Subtree related operations configuration.')
                ->children()
                    ->arrayNode('copy_subtree')
                        ->children()
                            ->integerNode('limit')
                                ->info('Number of items that can be copied at once, -1 for no limit, 0 to disable copying.')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
