<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\Values\Content\Query;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Provides information about mapping between serialized sort order and the value accepted by sort clause.
 *
 * @see \EzSystems\EzPlatformRest\Output\ValueObjectVisitor::serializeSortOrder
 */
class SortOrderMappings implements ProviderInterface
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'ASC' => Query::SORT_ASC,
            'DESC' => Query::SORT_DESC,
        ];
    }
}
