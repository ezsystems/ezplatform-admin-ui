<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText;

/**
 * Contracts for mapping Semantic configuration to settings exposed to templates.
 */
interface OnlineEditorConfigMapper
{
    /**
     * Map Online Editor custom CSS classes configuration.
     *
     * Configuration is exposed as:
     * <code>
     * ezpublish:
     *   system:
     *     <scope>:
     *       fieldtypes:
     *         ezrichtext:
     *           classes:
     * </code>
     *
     * @param array $semanticSemanticConfiguration
     *
     * @return array
     */
    public function mapCssClassesConfiguration(array $semanticSemanticConfiguration): array;

    /**
     * Map Online Editor custom data attributes classes configuration.
     *
     * Configuration is exposed as:
     * <code>
     * ezpublish:
     *   system:
     *     <scope>:
     *       fieldtypes:
     *         ezrichtext:
     *           attributes:
     * </code>
     *
     * @param array $semanticConfiguration
     *
     * @return array
     */
    public function mapDataAttributesConfiguration(array $semanticConfiguration): array;
}
