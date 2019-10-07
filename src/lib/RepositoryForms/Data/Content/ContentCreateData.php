<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content;

use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessCheckable;

/**
 * @property \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData[] $fieldsData
 */
class ContentCreateData extends ContentCreateStruct implements NewnessCheckable
{
    use ContentData;

    /**
     * @var LocationCreateStruct[]
     */
    private $locationStructs;

    public function isNew(): bool
    {
        return true;
    }

    /**
     * @return LocationCreateStruct[]
     */
    public function getLocationStructs()
    {
        return $this->locationStructs;
    }

    /**
     * Adds a location struct.
     * A location will be created out of it, bound to the created content.
     *
     * @param LocationCreateStruct $locationStruct
     */
    public function addLocationStruct(LocationCreateStruct $locationStruct)
    {
        $this->locationStructs[] = $locationStruct;
    }
}
