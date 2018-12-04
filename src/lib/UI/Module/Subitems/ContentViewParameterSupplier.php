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
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\REST\Common\Output\Visitor;
use eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\ContentTypeInfoList as ContentTypeInfoListValueObjectVisitor;
use eZ\Publish\Core\REST\Server\Values\ContentTypeInfoList;
use eZ\Publish\Core\REST\Server\Values\RestContent;
use eZ\Publish\Core\REST\Server\Values\RestLocation;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor\SubitemsList as SubitemsListValueObjectVisitor;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsList;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsRow;
use eZ\Publish\Core\REST\Common\Output\Generator\Json as JsonOutputGenerator;

/**
 * @internal
 */
class ContentViewParameterSupplier
{
    /** @var \eZ\Publish\Core\REST\Common\Output\Visitor */
    private $outputVisitor;

    /** @var \eZ\Publish\Core\REST\Common\Output\Generator\Json */
    private $outputGenerator;

    /** @var \eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\ContentTypeInfoList */
    private $contentTypeInfoListValueObjectVisitor;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor\SubitemsList */
    private $subitemsListValueObjectVisitor;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var int */
    private $subitemsLimit;

    /**
     * @param \eZ\Publish\Core\REST\Common\Output\Visitor $outputVisitor
     * @param \eZ\Publish\Core\REST\Common\Output\Generator\Json $outputGenerator
     * @param \eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\ContentTypeInfoList $contentTypeInfoListValueObjectVisitor
     * @param \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor\SubitemsList $subitemsListValueObjectVisitor
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param int $subitemsLimit
     */
    public function __construct(
        Visitor $outputVisitor,
        JsonOutputGenerator $outputGenerator,
        ContentTypeInfoListValueObjectVisitor $contentTypeInfoListValueObjectVisitor,
        SubitemsListValueObjectVisitor $subitemsListValueObjectVisitor,
        LocationService $locationService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        int $subitemsLimit
    ) {
        $this->outputVisitor = $outputVisitor;
        $this->outputGenerator = $outputGenerator;
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
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     *
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

        $locationChildren = $this->locationService->loadLocationChildren($location, 0, $this->subitemsLimit);
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
                /* @deprecated since version 2.2, to be removed in 3.0 */
                'limit' => $this->subitemsLimit,
                'content_type_info_list' => $contentTypeInfoListJson,
            ],
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \eZ\Publish\Core\REST\Server\Values\RestContent
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
     * @return \eZ\Publish\Core\REST\Server\Values\RestLocation
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
     * @param \eZ\Publish\Core\REST\Server\Values\ContentTypeInfoList $contentTypeInfoList
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
}
