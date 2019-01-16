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
 * Configuration parser for subtree related operations.
 *
 * Example configuration:
 *
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          content_type:
 *             article:
 *                thumbnail: '/assets/images/customarticle.svg'
 *             poll:
 *                thumbnail: '/assets/images/poll.svg'
 * ```
 */
class ContentType extends AbstractParser
{
    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings['content_type'])) {
            return;
        }

        $contextualizer->setContextualParameter('content_type', $currentScope, $scopeSettings['content_type']);
    }

    /**
     * {@inheritdoc}
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('content_type')
                ->useAttributeAsKey('identifier')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('thumbnail')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();
    }
}
