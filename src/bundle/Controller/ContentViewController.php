<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\REST\Common\Output\Generator\Json as JsonOutputGenerator;
use eZ\Publish\Core\REST\Common\Output\Visitor;
use eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\RestContentType;
use eZ\Publish\Core\REST\Server\Values\RestContent;
use eZ\Publish\Core\REST\Server\Values\RestLocation;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentDraftCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;

class ContentViewController extends Controller
{
    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var LanguageService */
    private $languageService;

    /** @var PathService */
    private $pathService;

    /** @var FormFactory */
    private $formFactory;

    /** @var Visitor */
    private $outputVisitor;

    /** @var JsonOutputGenerator */
    private $outputGenerator;

    /** @var \eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\RestContent */
    private $restContentValueObjectVisitor;

    /** @var \eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\RestLocation */
    private $restLocationValueObjectVisitor;

    /** @var LocationService */
    private $locationService;

    /** @var int */
    private $subitemsLimit;

    /** @var ContentService */
    private $contentService;

    /** @var RestContentType */
    private $restContentTypeValueObjectVisitor;

    /**
     * @param ContentTypeService $contentTypeService
     * @param LanguageService $languageService
     * @param PathService $pathService
     * @param FormFactory $formFactory
     * @param Visitor $outputVisitor
     * @param JsonOutputGenerator $outputGenerator
     * @param \eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\RestContent $restContentValueObjectVisitor
     * @param \eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\RestLocation $restLocationValueObjectVisitor
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        LanguageService $languageService,
        PathService $pathService,
        FormFactory $formFactory,
        Visitor $outputVisitor,
        JsonOutputGenerator $outputGenerator,
        \eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\RestContent $restContentValueObjectVisitor,
        \eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\RestLocation $restLocationValueObjectVisitor,
        LocationService $locationService,
        int $subitemsLimit,
        ContentService $contentService,
        RestContentType $restContentTypeValueObjectVisitor
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->languageService = $languageService;
        $this->pathService = $pathService;
        $this->formFactory = $formFactory;
        $this->outputVisitor = $outputVisitor;
        $this->outputGenerator = $outputGenerator;
        $this->restContentValueObjectVisitor = $restContentValueObjectVisitor;
        $this->restLocationValueObjectVisitor = $restLocationValueObjectVisitor;
        $this->locationService = $locationService;
        $this->subitemsLimit = $subitemsLimit;
        $this->contentService = $contentService;
        $this->restContentTypeValueObjectVisitor = $restContentTypeValueObjectVisitor;
    }

    public function locationViewAction(ContentView $view)
    {
        // We should not cache ContentView because we use forms with CSRF tokens in template
        // JIRA ref: https://jira.ez.no/browse/EZP-28190
        $view->setCacheEnabled(false);

        $this->supplyPathLocations($view);
        $this->supplyContentType($view);
        $this->supplyContentActionForms($view);
        $this->supplySubitems($view);

        return $view;
    }

    /**
     * @param ContentView $view
     */
    private function supplyPathLocations(ContentView $view): void
    {
        $location = $view->getLocation();
        $pathLocations = $this->pathService->loadPathLocations($location);
        $view->addParameters(['pathLocations' => $pathLocations]);
    }

    /**
     * @param ContentView $view
     */
    private function supplyContentType(ContentView $view): void
    {
        $content = $view->getContent();
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $view->addParameters(['contentType' => $contentType]);
    }

    private function supplyContentActionForms(ContentView $view): void
    {
        $location = $view->getLocation();
        $content = $view->getContent();
        $versionInfo = $content->getVersionInfo();

        $locationCopyType = $this->formFactory->copyLocation(
            new LocationCopyData($location)
        );

        $locationMoveType = $this->formFactory->moveLocation(
            new LocationMoveData($location)
        );

        $locationTrashType = $this->formFactory->trashLocation(
            new LocationTrashData($location)
        );

        $contentDraftCreateType = $this->formFactory->createContentDraft(
            new ContentDraftCreateData($content->contentInfo, $versionInfo)
        );

        $contentCreateType = $this->formFactory->createContent(
            $this->getContentCreateData($location)
        );

        $view->addParameters([
            'form_location_copy' => $locationCopyType->createView(),
            'form_location_move' => $locationMoveType->createView(),
            'form_location_trash' => $locationTrashType->createView(),
            'form_content_draft_create' => $contentDraftCreateType->createView(),
            'form_content_create' => $contentCreateType->createView(),
        ]);
    }

    /**
     * @param Location|null $location
     *
     * @return ContentCreateData
     */
    private function getContentCreateData(?Location $location): ContentCreateData
    {
        $languages = $this->languageService->loadLanguages();
        $language = 1 === count($languages)
            ? array_shift($languages)
            : null;

        return new ContentCreateData(null, $location, $language);
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
    private function supplySubitems(ContentView $view)
    {
        /** @var ContentType[] $contentTypes */
        $contentTypes = [];
        $items = [];

        $content = $view->getContent();
        $location = $view->getLocation();

        $locationChildren = $this->locationService->loadLocationChildren($location, 0, $this->subitemsLimit);
        foreach ($locationChildren->locations as $locationChild) {
            $contentInfo = $locationChild->getContentInfo();
            try {
                $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
            } catch (NotFoundException $e) {
                break;
            }

            if (!isset($contentTypes[$contentType->identifier])) {
                $contentTypes[$contentType->identifier] = $contentType;
            }

            $restLocation = new RestLocation(
                $locationChild,
                $this->locationService->getLocationChildCount($locationChild)
            );

            $this->outputGenerator->reset();
            $this->outputGenerator->startDocument($restLocation);
            $this->restLocationValueObjectVisitor->visit($this->outputVisitor, $this->outputGenerator, $restLocation);
            $locationJson = $this->outputGenerator->endDocument($restLocation);

            $restContent = new RestContent(
                $restLocation->location->getContentInfo(),
                $restLocation->location,
                $this->contentService->loadContentByContentInfo($contentInfo),
                $contentType,
                []
            );

            $this->outputGenerator->reset();
            $this->outputGenerator->startDocument($restContent);
            $this->restContentValueObjectVisitor->visit($this->outputVisitor, $this->outputGenerator, $restContent);
            $contentJson = $this->outputGenerator->endDocument($restContent);

            $items[] = sprintf("{'location': %s, 'content': %s}", $locationJson, $contentJson);
        }

        $contentTypesJson = '';
        foreach ($contentTypes as $contentType) {
            $restContentType = new \eZ\Publish\Core\REST\Server\Values\RestContentType($contentType);
            $this->outputGenerator->reset();
            $this->outputGenerator->startDocument($restContentType);
            $this->restContentTypeValueObjectVisitor->visit($this->outputVisitor, $this->outputGenerator, $restContentType);
            $contentTypeJson = $this->outputGenerator->endDocument($restContentType);
            $contentTypesJson .= sprintf('\'%s\': %s,', $contentType->remoteId, $contentTypeJson);
        }
        $contentTypesJson = '{' . $contentTypesJson . '}';

        $view->addParameters([
            'subitems_rows_json' => '[' . implode(', ', $items) . ']',
            'content_type_remote_id_map_json' => $contentTypesJson,
            'subitems_limit' => $this->subitemsLimit,
        ]);
    }
}
