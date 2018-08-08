<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\Location;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\TrashService;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashWithAssetData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationTrashWithAssetType;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrashLocationWithAssetController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\TrashService */
    private $trashService;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        LocationService $locationService,
        ContentService $contentService,
        TrashService $trashService
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->trashService = $trashService;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trashAction(Request $request): Response
    {
        $form = $this->formFactory->trashLocationWithAsset(
            new LocationTrashWithAssetData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LocationTrashWithAssetData $data) {
                $location = $data->getLocation();
                $parentLocation = $this->locationService->loadLocation($location->parentLocationId);
                if ($data->getTrashAssets() === LocationTrashWithAssetType::RADIO_SELECT_TRASH_WITH_ASSETS) {
                    $content = $this->contentService->loadContentByContentInfo($location->contentInfo);
                    $relations = $this->contentService->loadRelations($content->versionInfo);
                    $imageLocation = $this->locationService->loadLocation($relations[0]->destinationContentInfo->mainLocationId);
                    $this->trashService->trash($imageLocation);
                }

                $this->trashService->trash($location);

                return $this->redirectToLocation($parentLocation);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.trash.list'));
    }
}
