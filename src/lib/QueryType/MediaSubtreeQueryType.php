<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\QueryType;

use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\SubtreePath;

final class MediaSubtreeQueryType extends SubtreeQueryType
{
    public static function getName(): string
    {
        return 'EzPlatformAdminUi:MediaSubtree';
    }

    protected function getSubtreePathFromConfiguration(): string
    {
        return $this->configResolver->getParameter(SubtreePath::MEDIA_SUBTREE_PATH);
    }
}
