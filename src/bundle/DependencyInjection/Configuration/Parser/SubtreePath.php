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
 * Configuration parser for subtree path strings.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          subtree_paths:
 *              content: '/1/2/'
 *              media: '/1/43/'
 * ```
 */
class SubtreePath extends AbstractParser
{
    public const CONTENT_SUBTREE_PATH = 'subtree_paths.content';
    public const MEDIA_SUBTREE_PATH = 'subtree_paths.media';

    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('subtree_paths')
                ->info('Subtree paths configuration used as value of Subtree Query Criterion to load content on dashboard.')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('content')
                        ->info('Subtree path of Content to load proper Content on the Dashboard tabs')
                        ->defaultValue('/1/2/')
                    ->end()
                    ->scalarNode('media')
                        ->info('Subtree path of Media to load proper Content on the Dashboard tabs')
                        ->defaultValue('/1/43/')
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['subtree_paths'])) {
            return;
        }

        $settings = $scopeSettings['subtree_paths'];

        if (array_key_exists('content', $settings)) {
            $contextualizer->setContextualParameter(
                self::CONTENT_SUBTREE_PATH,
                $currentScope,
                $settings['content']
            );
        }

        if (array_key_exists('media', $settings)) {
            $contextualizer->setContextualParameter(
                self::MEDIA_SUBTREE_PATH,
                $currentScope,
                $settings['media']
            );
        }
    }
}
