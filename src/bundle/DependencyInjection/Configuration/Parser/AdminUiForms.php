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
 * Configuration parser for Admin UI forms settings.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per SiteAccess or SiteAccess group
 *          admin_ui_forms:
 *              content_edit_form_templates: ['template.html.twig']
 * ```
 */
class AdminUiForms extends AbstractParser
{
    const FORM_TEMPLATES_PARAM = 'admin_ui_forms.content_edit_form_templates';

    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('admin_ui_forms')
                ->info('Admin UI forms configuration settings')
                ->children()
                    ->arrayNode('content_edit_form_templates')
                        ->info('A list of Content Edit (and create) default Twig form templates')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        $contextualizer->setContextualParameter(
            static::FORM_TEMPLATES_PARAM,
            $currentScope,
            $scopeSettings['admin_ui_forms']['content_edit_form_templates'] ?? []
        );
    }
}
