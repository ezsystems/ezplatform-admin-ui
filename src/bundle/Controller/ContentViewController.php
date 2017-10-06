<?php

namespace EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzPlatformAdminUi\Form\Factory\FormFactory;
use EzPlatformAdminUi\Service\PathService;

class ContentViewController extends Controller
{
    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var PathService */
    protected $pathService;

    /** @var FormFactory */
    protected $formFactory;

    /**
     * @param ContentTypeService $contentTypeService
     * @param PathService $pathService
     * @param FormFactory $formFactory
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        PathService $pathService,
        FormFactory $formFactory
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->pathService = $pathService;
        $this->formFactory = $formFactory;
    }

    public function locationViewAction(ContentView $view)
    {
        $this->supplyPathLocations($view);
        $this->supplyContentType($view);
        $this->supplyContentActionForms($view);

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
        $locationViewUrl = $this->generateUrl($location);

        $locationCopyType = $this->formFactory->copyLocation(
            null,
            new LocationCopyData($location),
            null /* action handles the redirection */,
            $locationViewUrl
        );
        $locationMoveType = $this->formFactory->moveLocation(
            null,
            new LocationMoveData($location),
            null /* action handles the redirection */,
            $locationViewUrl
        );
        $locationTrashType = $this->formFactory->trashLocation(
            null,
            new LocationTrashData($location),
            null /* action handles the redirection */,
            $locationViewUrl
        );

        $view->addParameters([
            'form_location_copy' => $locationCopyType->createView(),
            'form_location_move' => $locationMoveType->createView(),
            'form_location_trash' => $locationTrashType->createView(),
        ]);
    }
}
