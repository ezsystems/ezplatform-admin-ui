<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupUpdateData;

class ContentTypeGroupMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup|ValueObject $contentTypeGroup
     * @param array $params
     *
     * @return ContentTypeGroupCreateData|ContentTypeGroupUpdateData
     */
    public function mapToFormData(ValueObject $contentTypeGroup, array $params = [])
    {
        if (!$this->isContentTypeGroupNew($contentTypeGroup)) {
            $data = new ContentTypeGroupUpdateData(['contentTypeGroup' => $contentTypeGroup]);
            $data->identifier = $contentTypeGroup->identifier;
        } else {
            $data = new ContentTypeGroupCreateData(['contentTypeGroup' => $contentTypeGroup]);
        }

        return $data;
    }

    private function isContentTypeGroupNew(ContentTypeGroup $contentTypeGroup)
    {
        return $contentTypeGroup->id === null;
    }
}
