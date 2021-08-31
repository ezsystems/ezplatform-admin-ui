<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;

abstract class AbstractRelationFormMapper implements FieldDefinitionFormMapperInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    public function __construct(ContentTypeService $contentTypeService, LocationService $locationService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
    }

    /**
     * Fill a hash with all content types and their ids.
     *
     * @return string[]
     */
    protected function getContentTypesHash(): array
    {
        $contentTypeHash = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $contentTypeGroup) {
            foreach ($this->contentTypeService->loadContentTypes($contentTypeGroup) as $contentType) {
                $contentTypeHash[$contentType->getName()] = $contentType->identifier;
            }
        }
        ksort($contentTypeHash);

        return $contentTypeHash;
    }
}
