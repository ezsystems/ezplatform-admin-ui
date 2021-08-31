<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\Subitems;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\EzPlatformAdminUi\UI\Config\Provider\ContentTypeMappings;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor\SubitemsList as SubitemsListValueObjectVisitor;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsList;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsRow;
use EzSystems\EzPlatformRest\Output\Generator\Json as JsonOutputGenerator;
use EzSystems\EzPlatformRest\Output\Visitor;
use EzSystems\EzPlatformRest\Server\Output\ValueObjectVisitor\ContentTypeInfoList as ContentTypeInfoListValueObjectVisitor;
use EzSystems\EzPlatformRest\Server\Values\ContentTypeInfoList;
use EzSystems\EzPlatformRest\Server\Values\RestContent;
use EzSystems\EzPlatformRest\Server\Values\RestLocation;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;

/**
 * @internal
 */
class ContentViewParameterSupplier
{
    /** @var \EzSystems\EzPlatformRest\Output\Visitor */
    private $outputVisitor;

    /** @var \EzSystems\EzPlatformRest\Output\Generator\Json */
    private $outputGenerator;

    /** @var \EzSystems\EzPlatformRest\Server\Output\ValueObjectVisitor\ContentTypeInfoList */
    private $contentTypeInfoListValueObjectVisitor;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor\SubitemsList */
    private $subitemsListValueObjectVisitor;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Config\Provider\ContentTypeMappings */
    private $contentTypeMappings;

    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    private $userSettingService;

    /**
     * @param \EzSystems\EzPlatformRest\Output\Visitor $outputVisitor
     * @param \EzSystems\EzPlatformRest\Output\Generator\Json $outputGenerator
     * @param \EzSystems\EzPlatformRest\Server\Output\ValueObjectVisitor\ContentTypeInfoList $contentTypeInfoListValueObjectVisitor
     * @param \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor\SubitemsList $subitemsListValueObjectVisitor
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\UI\Config\Provider\ContentTypeMappings $contentTypeMappings
     * @param \EzSystems\EzPlatformUser\UserSetting\UserSettingService $userSettingService
     */
    public function __construct(
        Visitor $outputVisitor,
        JsonOutputGenerator $outputGenerator,
        ContentTypeInfoListValueObjectVisitor $contentTypeInfoListValueObjectVisitor,
        SubitemsListValueObjectVisitor $subitemsListValueObjectVisitor,
        LocationService $locationService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        PermissionResolver $permissionResolver,
        ContentTypeMappings $contentTypeMappings,
        UserSettingService $userSettingService
    ) {
        $this->outputVisitor = $outputVisitor;
        $this->outputGenerator = $outputGenerator;
        $this->contentTypeInfoListValueObjectVisitor = $contentTypeInfoListValueObjectVisitor;
        $this->subitemsListValueObjectVisitor = $subitemsListValueObjectVisitor;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->permissionResolver = $permissionResolver;
        $this->contentTypeMappings = $contentTypeMappings;
        $this->userSettingService = $userSettingService;
    }

    /**
     * Fetches data for Subitems module to populate it with preloaded data.
     *
     * Why are we using REST stuff here?
     *
     * This is not so elegant but to preload data in Subitems module
     * we are using the same data structure it would use while
     * fetching data from the REST.
     *
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function supply(ContentView $view)
    {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType[] $contentTypes */
        $contentTypes = [];
        $subitemsRows = [];
        $location = $view->getLocation();
        $childrenCount = $this->locationService->getLocationChildCount($location);

        $subitemsLimit = (int)$this->userSettingService->getUserSetting('subitems_limit')->value;

        $locationChildren = $this->locationService->loadLocationChildren($location, 0, $subitemsLimit);
        foreach ($locationChildren->locations as $locationChild) {
            $contentType = $locationChild->getContent()->getContentType();

            if (!isset($contentTypes[$contentType->identifier])) {
                $contentTypes[$contentType->identifier] = $contentType;
            }

            $subitemsRows[] = $this->createSubitemsRow($locationChild, $contentType);
        }

        $subitemsList = new SubitemsList($subitemsRows, $childrenCount);
        $contentTypeInfoList = new ContentTypeInfoList($contentTypes, '');

        $subitemsListJson = $this->visitSubitemsList($subitemsList);
        $contentTypeInfoListJson = $this->visitContentTypeInfoList($contentTypeInfoList);

        $view->addParameters([
            'subitems_module' => [
                'items' => $subitemsListJson,
                'content_type_info_list' => $contentTypeInfoListJson,
                'content_create_permissions_for_mfu' => $this->getContentCreatePermissionsForMFU($view->getLocation(), $view->getContent()),
            ],
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \EzSystems\EzPlatformRest\Server\Values\RestContent
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function createRestContent(
        Location $location,
        ContentType $contentType
    ): RestContent {
        return new RestContent(
            $location->getContentInfo(),
            $location,
            $location->getContent(),
            $contentType,
            []
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \EzSystems\EzPlatformRest\Server\Values\RestLocation
     */
    private function createRestLocation(Location $location): RestLocation
    {
        return new RestLocation(
            $location,
            $this->locationService->getLocationChildCount($location)
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsRow
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function createSubitemsRow(
        Location $location,
        ContentType $contentType
    ): SubitemsRow {
        $restLocation = $this->createRestLocation($location);
        $restContent = $this->createRestContent($location, $contentType);

        return new SubitemsRow($restLocation, $restContent);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsList $subitemsList
     *
     * @return string
     */
    private function visitSubitemsList(SubitemsList $subitemsList): string
    {
        $this->outputGenerator->reset();
        $this->outputGenerator->startDocument($subitemsList);
        $this->subitemsListValueObjectVisitor->visit($this->outputVisitor, $this->outputGenerator, $subitemsList);

        return $this->outputGenerator->endDocument($subitemsList);
    }

    /**
     * @param \EzSystems\EzPlatformRest\Server\Values\ContentTypeInfoList $contentTypeInfoList
     *
     * @return string
     */
    private function visitContentTypeInfoList(ContentTypeInfoList $contentTypeInfoList): string
    {
        $this->outputGenerator->reset();
        $this->outputGenerator->startDocument($contentTypeInfoList);
        $this->contentTypeInfoListValueObjectVisitor->visit($this->outputVisitor, $this->outputGenerator, $contentTypeInfoList);

        return $this->outputGenerator->endDocument($contentTypeInfoList);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function getContentCreatePermissionsForMFU(Location $location, Content $content): array
    {
        $createPermissionsInMfu = [];

        $hasAccess = $this->permissionResolver->hasAccess('content', 'create');
        $defaultContentTypeIdentifiers = array_column($this->contentTypeMappings->getConfig()['defaultMappings'], 'contentTypeIdentifier');
        $defaultContentTypeIdentifiers[] = $this->contentTypeMappings->getConfig()['fallbackContentType']['contentTypeIdentifier'];
        $contentTypeIdentifiers = array_unique($defaultContentTypeIdentifiers);

        if (\is_bool($hasAccess)) {
            foreach ($contentTypeIdentifiers as $contentTypeIdentifier) {
                $createPermissionsInMfu[$contentTypeIdentifier] = $hasAccess;
            }
        } else {
            $locationCreateStruct = $this->locationService->newLocationCreateStruct($location->id);
            foreach ($contentTypeIdentifiers as $contentTypeIdentifier) {
                // TODO: Change to `contentTypeService->loadContentTypeList($restrictedContentTypesIds)` after #2444 will be merged
                $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
                $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $content->versionInfo->initialLanguageCode);
                $contentCreateStruct->sectionId = $location->contentInfo->sectionId;
                $createPermissionsInMfu[$contentTypeIdentifier] = $this->permissionResolver->canUser('content', 'create', $contentCreateStruct, [$locationCreateStruct]);
            }
        }

        return $createPermissionsInMfu;
    }
}
