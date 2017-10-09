<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Service\ContentTypeGroup;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroupData;

class ContentTypeGroupService
{
    /** @var  ContentTypeService */
    private $contentTypeService;

    /**
     * ContentTypeGroupService constructor.
     *
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function getContentTypeGroup(int $id): ContentTypeGroup
    {
        return $this->contentTypeService->loadContentTypeGroup($id);
    }

    public function getContentTypeGroups(): array
    {
        return $this->contentTypeService->loadContentTypeGroups();
    }

    public function createContentTypeGroup(ContentTypeGroupData $data): ContentTypeGroup
    {
        $createStruct = $this->contentTypeService->newContentTypeGroupCreateStruct(
            $data->getIdentifier()
        );

        return $this->contentTypeService->createContentTypeGroup($createStruct);
    }

    public function updateContentTypeGroup(ContentTypeGroup $group, ContentTypeGroupData $data): ContentTypeGroup
    {
        $updateStruct = $this->contentTypeService->newContentTypeGroupUpdateStruct();
        $updateStruct->identifier = $data->getIdentifier();

        $this->contentTypeService->updateContentTypeGroup($group, $updateStruct);

        return $group;
    }

    public function deleteContentTypeGroup(ContentTypeGroup $group)
    {
        $this->contentTypeService->deleteContentTypeGroup($group);
    }
}
