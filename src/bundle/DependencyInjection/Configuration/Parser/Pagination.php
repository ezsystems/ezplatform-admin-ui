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
 * Configuration parser for pagination limits.
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
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('pagination')
                ->info('System pagination configuration')
                ->children()
                    ->scalarNode('search_limit')
                        ->isRequired()
                        ->setDeprecated('ezsystems/ezplatform-admin-ui', '2.1', 'The child node "%node%" at path "%path%" is deprecated. Use "search.pagination.limit" instead.')
                    ->end()
                    ->scalarNode('trash_limit')->isRequired()->end()
                    ->scalarNode('section_limit')->isRequired()->end()
                    ->scalarNode('language_limit')->isRequired()->end()
                    ->scalarNode('role_limit')->isRequired()->end()
                    ->scalarNode('role_assignment_limit')->isRequired()->end()
                    ->scalarNode('policy_limit')->isRequired()->end()
                    ->scalarNode('content_type_group_limit')->isRequired()->end()
                    ->scalarNode('content_type_limit')->isRequired()->end()
                    ->scalarNode('version_draft_limit')->isRequired()->end()
                    ->scalarNode('reverse_relation_limit')->isRequired()->end()
                    ->scalarNode('content_system_url_limit')->isRequired()->end()
                    ->scalarNode('content_custom_url_limit')->isRequired()->end()
                    ->scalarNode('content_role_limit')->isRequired()->end()
                    ->scalarNode('content_policy_limit')->isRequired()->end()
                    ->scalarNode('notification_limit')->isRequired()->end()
                    ->scalarNode('content_draft_limit')->isRequired()->end()
                    ->scalarNode('location_limit')->isRequired()->end()
                ->end()
            ->end();
    }

    /**
     * @inheritdoc
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
            'role_limit',
            'role_assignment_limit',
            'policy_limit',
            'content_type_group_limit',
            'content_type_limit',
            'version_draft_limit',
            'reverse_relation_limit',
            'content_system_url_limit',
            'content_custom_url_limit',
            'content_role_limit',
            'content_policy_limit',
            'notification_limit',
            'content_draft_limit',
            'location_limit',
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
