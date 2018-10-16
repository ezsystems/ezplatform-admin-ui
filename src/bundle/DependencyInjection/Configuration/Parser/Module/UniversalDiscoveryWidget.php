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
 * Configuration parser for UDW module.
 */
class UniversalDiscoveryWidget extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('universal_discovery_widget_module')
                ->info('UDW module configuration')
                ->children()
                    ->scalarNode('default_location_id')
                        ->setDeprecated('Use configuration.default.starting_location_id instead.')
                        ->isRequired()
                    ->end()
                    ->arrayNode('configuration')
                        ->isRequired()
                        ->useAttributeAsKey('scope_name')
                        ->variablePrototype()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['universal_discovery_widget_module'])) {
            return;
        }

        $settings = $scopeSettings['universal_discovery_widget_module'];

        if (!isset($settings['default_location_id']) || empty($settings['default_location_id'])) {
            return;
        }

        $contextualizer->setContextualParameter(
            'universal_discovery_widget_module.default_location_id',
            $currentScope,
            $settings['default_location_id']
        );

        $contextualizer->setContextualParameter(
            'universal_discovery_widget_module.configuration',
            $currentScope,
            $settings['configuration']
        );
    }
}
