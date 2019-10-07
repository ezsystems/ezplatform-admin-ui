<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class UserEdit extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('user_edit')
                ->info('Content edit configuration')
                ->children()
                    ->arrayNode('templates')
                        ->info('Content edit templates.')
                        ->children()
                            ->scalarNode('update')
                                ->info('Template to use for user edit form rendering.')
                            ->end()
                            ->scalarNode('create')
                                ->info('Template to use for user create form rendering.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings['user_edit'])) {
            return;
        }

        $settings = $scopeSettings['user_edit'];

        if (!empty($settings['templates']['update'])) {
            $contextualizer->setContextualParameter(
                'user_edit.templates.update',
                $currentScope,
                $settings['templates']['update']
            );
        }

        if (!empty($settings['templates']['create'])) {
            $contextualizer->setContextualParameter(
                'user_edit.templates.create',
                $currentScope,
                $settings['templates']['create']
            );
        }
    }
}
