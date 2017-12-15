<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ContentViewParameterSupplier as SubitemsContentViewParameterSupplier;
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

    /** @var SubitemsContentViewParameterSupplier */
    private $subitemsContentViewParameterSupplier;

    public function __construct(
        ContentTypeService $contentTypeService,
        LanguageService $languageService,
        PathService $pathService,
        FormFactory $formFactory,
        SubitemsContentViewParameterSupplier $subitemsContentViewParameterSupplier
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->languageService = $languageService;
        $this->pathService = $pathService;
        $this->formFactory = $formFactory;
        $this->subitemsContentViewParameterSupplier = $subitemsContentViewParameterSupplier;
    }

    public function locationViewAction(ContentView $view)
    {
        // We should not cache ContentView because we use forms with CSRF tokens in template
        // JIRA ref: https://jira.ez.no/browse/EZP-28190
        $view->setCacheEnabled(false);

        $this->supplyPathLocations($view);
        $this->supplyContentType($view);
        $this->supplyContentActionForms($view);
        $this->subitemsContentViewParameterSupplier->supply($view);

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

        $contentEditType = $this->formFactory->contentEdit(
            new ContentEditData($content->contentInfo, $versionInfo)
        );

        $contentCreateType = $this->formFactory->createContent(
            $this->getContentCreateData($location)
        );

        $view->addParameters([
            'form_location_copy' => $locationCopyType->createView(),
            'form_location_move' => $locationMoveType->createView(),
            'form_location_trash' => $locationTrashType->createView(),
            'form_content_edit' => $contentEditType->createView(),
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
}
