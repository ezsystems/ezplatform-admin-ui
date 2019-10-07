<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\FieldType\FieldValueFormMapperInterface;

abstract class AbstractRelationFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService Used to fetch list of available content types
     */
    protected $contentTypeService;

    /**
     * @var \eZ\Publish\API\Repository\LocationService Used to fetch selection root
     */
    protected $locationService;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(ContentTypeService $contentTypeService, LocationService $locationService)
    {
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
    }

    /**
     * Fill a hash with all content types and their ids.
     *
     * @return array
     */
    protected function getContentTypesHash()
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

    /**
     * Loads location which is starting point for selecting destination content object.
     *
     * @param null $defaultLocationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    protected function loadDefaultLocationForSelection($defaultLocationId = null): ?Location
    {
        if (!empty($defaultLocationId)) {
            try {
                return $this->locationService->loadLocation((int)$defaultLocationId);
            } catch (NotFoundException | UnauthorizedException $e) {
            }
        }

        return null;
    }
}
