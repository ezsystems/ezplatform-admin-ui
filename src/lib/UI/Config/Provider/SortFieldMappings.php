<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Provides information about mapping between serialized sort field and the value accepted by sort clause.
 *
 * @see \EzSystems\EzPlatformRest\Output\ValueObjectVisitor::serializeSortField
 */
class SortFieldMappings implements ProviderInterface
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
           'PATH' => 'LocationPath',
           'PUBLISHED' => 'DatePublished',
           'MODIFIED' => 'DateModified',
           'SECTION' => 'SectionIdentifier',
           'DEPTH' => 'LocationDepth',
           'PRIORITY' => 'LocationPriority',
           'NAME' => 'ContentName',
           'NODE_ID' => 'LocationId',
           'CONTENTOBJECT_ID' => 'ContentId',
        ];
    }
}
