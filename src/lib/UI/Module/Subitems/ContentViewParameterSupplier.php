<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\Subitems;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\REST\Common\Output\Visitor;
use eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\ContentTypeInfoList as ContentTypeInfoListValueObjectVisitor;
use eZ\Publish\Core\REST\Server\Values\ContentTypeInfoList;
use eZ\Publish\Core\REST\Server\Values\RestContent;
use eZ\Publish\Core\REST\Server\Values\RestLocation;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor\SubitemsRow as SubitemsRowValueObjectVisitor;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor\SubitemsList as SubitemsListValueObjectVisitor;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsList;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsRow;
use eZ\Publish\Core\REST\Common\Output\Generator\Json as JsonOutputGenerator;

/**
 * @internal
 */
class ContentViewParameterSupplier
{
    /** @var Visitor */
    private $outputVisitor;

    /** @var JsonOutputGenerator */
    private $outputGenerator;

    /** @var SubitemsRowValueObjectVisitor */
    private $subitemsRowValueObjectVisitor;

    /** @var ContentTypeInfoListValueObjectVisitor */
    private $contentTypeInfoListValueObjectVisitor;

    /** @var SubitemsListValueObjectVisitor */
    private $subitemsListValueObjectVisitor;

    /** @var LocationService */
    private $locationService;

    /** @var ContentService */
    private $contentService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var int */
    private $subitemsLimit;

    /**
     * @param Visitor $outputVisitor
     * @param JsonOutputGenerator $outputGenerator
     * @param SubitemsRowValueObjectVisitor $subitemsRowValueObjectVisitor
     * @param ContentTypeInfoListValueObjectVisitor $contentTypeInfoListValueObjectVisitor
     * @param LocationService $locationService
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     * @param int $subitemsLimit
     */
    public function __construct(
        Visitor $outputVisitor,
        JsonOutputGenerator $outputGenerator,
        SubitemsRowValueObjectVisitor $subitemsRowValueObjectVisitor,
        ContentTypeInfoListValueObjectVisitor $contentTypeInfoListValueObjectVisitor,
        SubitemsListValueObjectVisitor $subitemsListValueObjectVisitor,
        LocationService $locationService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        int $subitemsLimit
    ) {
        $this->outputVisitor = $outputVisitor;
        $this->outputGenerator = $outputGenerator;
        $this->subitemsRowValueObjectVisitor = $subitemsRowValueObjectVisitor;
        $this->contentTypeInfoListValueObjectVisitor = $contentTypeInfoListValueObjectVisitor;
        $this->subitemsListValueObjectVisitor = $subitemsListValueObjectVisitor;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->subitemsLimit = $subitemsLimit;
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
     * @param ContentView $view
     */
    public function supply(ContentView $view)
    {
        /** @var ContentType[] $contentTypes */
        $contentTypes = [];
        $subitemsRows = [];
        $location = $view->getLocation();
        $childrenCount = $this->locationService->getLocationChildCount($location);

        $locationChildren = $this->locationService->loadLocationChildren($location, 0, $this->subitemsLimit);
        foreach ($locationChildren->locations as $locationChild) {
            $contentInfo = $locationChild->getContentInfo();
            $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

            if (!isset($contentTypes[$contentType->identifier])) {
                $contentTypes[$contentType->identifier] = $contentType;
            }

            $subitemsRows[] = $this->createSubitemsRow($locationChild, $contentInfo, $contentType);
        }

        $subitemsList = new SubitemsList($subitemsRows, $childrenCount);
        $contentTypeInfoList = new ContentTypeInfoList($contentTypes, '');

        $subitemsListJson = $this->visitSubitemsList($subitemsList);
        $contentTypeInfoListJson = $this->visitContentTypeInfoList($contentTypeInfoList);

        $view->addParameters([
            'subitems_module' => [
                'items' => $subitemsListJson,
                'limit' => $this->subitemsLimit,
                'content_type_info_list' => $contentTypeInfoListJson,
            ],
        ]);
    }

    /**
     * @param ContentInfo $contentInfo
     * @param Location $location
     * @param ContentType $contentType
     *
     * @return RestContent
     */
    private function createRestContent(
        ContentInfo $contentInfo,
        Location $location,
        ContentType $contentType
    ): RestContent {
        return new RestContent(
            $contentInfo,
            $location,
            $this->contentService->loadContentByContentInfo($contentInfo),
            $contentType,
            []
        );
    }

    /**
     * @param Location $location
     *
     * @return RestLocation
     */
    private function createRestLocation(Location $location): RestLocation
    {
        return new RestLocation(
            $location,
            $this->locationService->getLocationChildCount($location)
        );
    }

    /**
     * @param Location $location
     * @param ContentInfo $contentInfo
     * @param ContentType $contentType
     *
     * @return SubitemsRow
     */
    private function createSubitemsRow(
        Location $location,
        ContentInfo $contentInfo,
        ContentType $contentType
    ): SubitemsRow {
        $restLocation = $this->createRestLocation($location);
        $restContent = $this->createRestContent($contentInfo, $location, $contentType);

        return new SubitemsRow($restLocation, $restContent);
    }

    /**
     * @param $subitemsList
     *
     * @return string
     */
    private function visitSubitemsList($subitemsList): string
    {
        $this->outputGenerator->reset();
        $this->outputGenerator->startDocument($subitemsList);
        $this->subitemsListValueObjectVisitor->visit($this->outputVisitor, $this->outputGenerator, $subitemsList);

        return $this->outputGenerator->endDocument($subitemsList);
    }

    /**
     * @param $contentTypeInfoList
     *
     * @return string
     */
    private function visitContentTypeInfoList($contentTypeInfoList): string
    {
        $this->outputGenerator->reset();
        $this->outputGenerator->startDocument($contentTypeInfoList);
        $this->contentTypeInfoListValueObjectVisitor->visit($this->outputVisitor, $this->outputGenerator, $contentTypeInfoList);

        return $this->outputGenerator->endDocument($contentTypeInfoList);
    }
}
