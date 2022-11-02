<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\QueryType;

use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\SubtreePath;

final class ContentLocationSubtreeQueryType extends LocationSubtreeQueryType
{
    public static function getName(): string
    {
        return 'IbexaAdminUi:ContentLocationSubtree';
    }

    protected function getSubtreePathFromConfiguration(): string
    {
        return $this->configResolver->getParameter(SubtreePath::CONTENT_SUBTREE_PATH);
    }
}
