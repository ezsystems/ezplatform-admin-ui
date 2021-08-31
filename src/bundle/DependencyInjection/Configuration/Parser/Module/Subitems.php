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
 *          subitems_module:
 *              limit: 10
 * ```
 */
class Subitems extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('subitems_module')
            ->info('Subitems module configuration')
            ->children()
            ->integerNode('limit')->isRequired()->defaultValue(10)->end()
            ->end()
            ->end();
    }

    /**
     * @inheritdoc
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['subitems_module'])) {
            return;
        }

        $settings = $scopeSettings['subitems_module'];

        if (!isset($settings['limit'])) {
            return;
        }

        $contextualizer->setContextualParameter(
            'subitems_module.limit',
            $currentScope,
            $settings['limit']
        );
    }
}
