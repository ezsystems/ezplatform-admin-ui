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
 *      admin_group: # configuration per siteaccess or siteaccess group
 *          notifications:
 *              warning: # type of notification
 *                  timeout: 5000 # in milliseconds
 * ```
 */
class Notifications extends AbstractParser
{
    /**
     * @inheritdoc
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings['notifications'])) {
            return;
        }

        $settings = $scopeSettings['notifications'];
        $nodes = ['timeout'];

        foreach ($settings as $type => $config) {
            foreach ($nodes as $key) {
                if (!isset($config[$key]) || empty($config[$key])) {
                    continue;
                }

                $contextualizer->setContextualParameter(
                    sprintf('notifications.%s.%s', $type, $key),
                    $currentScope,
                    $config[$key]
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
            ->arrayNode('notifications')
                ->useAttributeAsKey('type')
                ->info('AdminUI notifications configuration.')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('timeout')
                            ->info('Time in milliseconds notifications should disappear after.')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
