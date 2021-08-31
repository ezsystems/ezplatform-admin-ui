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
 * Configuration parser for location ids declaration.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          location_ids:
 *              content_structure: 2
 *              media: 43
 *              users: 5
 * ```
 */
class LocationIds extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('location_ids')
                ->info('System locations id configuration')
                ->children()
                    ->scalarNode('content_structure')->isRequired()->end()
                    ->scalarNode('media')->isRequired()->end()
                    ->scalarNode('users')->isRequired()->end()
                ->end()
            ->end();
    }

    /**
     * @inheritdoc
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['location_ids'])) {
            return;
        }

        $settings = $scopeSettings['location_ids'];
        $keys = ['content_structure', 'media', 'users'];

        foreach ($keys as $key) {
            if (!isset($settings[$key]) || empty($settings[$key])) {
                continue;
            }

            $contextualizer->setContextualParameter(
                sprintf('location_ids.%s', $key),
                $currentScope,
                $settings[$key]
            );
        }
    }
}
