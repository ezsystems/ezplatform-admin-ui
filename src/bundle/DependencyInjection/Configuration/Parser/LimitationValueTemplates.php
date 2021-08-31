<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\Templates;

class LimitationValueTemplates extends Templates
{
    const NODE_KEY = 'limitation_value_templates';
    const INFO = 'Settings for limitation value templates';
    const INFO_TEMPLATE_KEY = 'Template file where to find block definition to display limitation values';
}
