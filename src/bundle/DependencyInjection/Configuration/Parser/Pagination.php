<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
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
 *          pagination:
 *              search_limit: 10
 *              trash_limit: 10
 *              section_limit: 10
 *              language_limit: 10
 * ```
 */
class Pagination extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('pagination')
                ->info('System pagination configuration')
                ->children()
                    ->scalarNode('search_limit')->isRequired()->end()
                    ->scalarNode('trash_limit')->isRequired()->end()
                    ->scalarNode('section_limit')->isRequired()->end()
                    ->scalarNode('language_limit')->isRequired()->end()
                    ->scalarNode('role_assignment_limit')->isRequired()->end()
                    ->scalarNode('policy_limit')->isRequired()->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['pagination'])) {
            return;
        }

        $settings = $scopeSettings['pagination'];
        $keys = [
            'search_limit',
            'trash_limit',
            'section_limit',
            'language_limit',
            'role_assignment_limit',
            'policy_limit',
        ];

        foreach ($keys as $key) {
            if (!isset($settings[$key]) || empty($settings[$key])) {
                continue;
            }

            $contextualizer->setContextualParameter(
                sprintf('pagination.%s', $key),
                $currentScope,
                $settings[$key]
            );
        }
    }
}
