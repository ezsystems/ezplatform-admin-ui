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
 * Configuration parser for user preferences.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          user_preferences:
 *              additional_translations: ['en_US', 'en_GB']
 * ```
 */
class UserPreferences extends AbstractParser
{
    /**
     * {@inheritdoc}
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('user_preferences')
                ->info('User Preferences configuration.')
                ->children()
                    ->arrayNode('additional_translations')
                        ->info('Additional translations to display on the preferred language list.')
                        ->example(['en_US', 'en_GB'])
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['user_preferences']['additional_translations'])) {
            return;
        }

        $contextualizer->setContextualParameter(
            'user_preferences.additional_translations',
            $currentScope,
            $scopeSettings['user_preferences']['additional_translations']
        );
    }
}
