<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\QueryType;

use Ibexa\Bundle\AdminUi\DependencyInjection\Configuration\Parser\SubtreePath;

final class ContentSubtreeQueryType extends SubtreeQueryType
{
    public static function getName(): string
    {
        return 'EzPlatformAdminUi:ContentSubtree';
    }

    protected function getSubtreePathFromConfiguration(): string
    {
        return $this->configResolver->getParameter(SubtreePath::CONTENT_SUBTREE_PATH);
    }
}

class_alias(ContentSubtreeQueryType::class, 'EzSystems\EzPlatformAdminUi\QueryType\ContentSubtreeQueryType');
