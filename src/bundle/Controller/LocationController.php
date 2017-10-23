<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\Data\UiFormData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class LocationController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentService */
    private $contentService;

    /** @var LocationService */
    private $locationService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var TrashService */
    private $trashService;

    /** @var FormFactory */
    private $formFactory;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param LocationService $locationService
     * @param ContentTypeService $contentTypeService
     * @param ContentService $contentService
     * @param TrashService $trashService
     * @param FormFactory $formFactory
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        TrashService $trashService,
        FormFactory $formFactory
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->trashService = $trashService;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function moveAction(Request $request): Response
    {
        $form = $this->formFactory->moveLocation();
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();
        /** @var LocationMoveData $locationMoveData */
        $locationMoveData = $uiFormData->getData();

        if ($form->isValid() && $form->isSubmitted()) {
            $location = $locationMoveData->getLocation();
            $newParentLocation = $locationMoveData->getNewParentLocation();

            /** @todo move it into the service */
            $newParentContentType = $this->contentTypeService->loadContentType(
                $newParentLocation->getContentInfo()->contentTypeId
            );
            if (!$newParentContentType->isContainer) {
                throw new InvalidArgumentException(
                    '$newParentLocation',
                    'Cannot move location to a parent that is not a container'
                );
            }
            $this->locationService->moveSubtree($location, $newParentLocation);

            $this->flashSuccess('location.move.success', [
                '%locationName%' => $location->getContentInfo()->name,
            ], 'location');

            return $this->redirectToRoute($location);
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function copyAction(Request $request): Response
    {
        $form = $this->formFactory->copyLocation();
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();
        /** @var LocationCopyData $locationCopyData */
        $locationCopyData = $uiFormData->getData();

        if ($form->isValid() && $form->isSubmitted()) {
            $location = $locationCopyData->getLocation();
            $newParentLocation = $locationCopyData->getNewParentLocation();

            /** @todo move it into the service */
            $newParentContentType = $this->contentTypeService->loadContentType(
                $newParentLocation->getContentInfo()->contentTypeId
            );

            /** @todo this should be validated on form level */
            if (!$newParentContentType->isContainer) {
                throw new InvalidArgumentException(
                    '$newParentLocation',
                    'Cannot copy location to a parent that is not a container'
                );
            }

            $locationCreateStruct = $this->locationService->newLocationCreateStruct($newParentLocation->id);
            $copiedContent = $this->contentService->copyContent(
                $location->contentInfo,
                $locationCreateStruct
            );

            $newLocation = $this->locationService->loadLocation($copiedContent->contentInfo->mainLocationId);

            $this->flashSuccess('location.copy.success', [
                '%locationName%' => $copiedContent->getName(),
            ], 'location');

            return $this->redirectToRoute($newLocation);
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirectToRoute($location);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function swapAction(Request $request): RedirectResponse
    {
        $form = $this->formFactory->swapLocation();
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();
        /** @var LocationSwapData $locationSwapData */
        $locationSwapData = $uiFormData->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @todo empty locations should be validated on form level */
            /* @todo add validation to data class */
            $currentLocation = $locationSwapData->getCurrentLocation();
            $newLocation = $locationSwapData->getNewLocation();

            $childCount = $this->locationService->getLocationChildCount($currentLocation);
            $contentType = $this->contentTypeService->loadContentType($newLocation->getContentInfo()->contentTypeId);

            if (!$contentType->isContainer && $childCount) {
                throw new \InvalidArgumentException(
                    'Cannot swap location that has sub items with a location that is not a container'
                );
            }
            $this->locationService->swapLocation($currentLocation, $newLocation);

            $this->flashSuccess('location.swap.success', [
                '%oldLocationName%' => $currentLocation->getContentInfo()->name,
                '%newLocationName%' => $newLocation->getContentInfo()->name,
            ], 'location');

            return $this->redirect($uiFormData->getOnSuccessRedirectionUrl());
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function trashAction(Request $request): Response
    {
        $form = $this->formFactory->trashLocation();
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();
        /** @var LocationTrashData $locationTrashData */
        $locationTrashData = $uiFormData->getData();

        $location = $locationTrashData->getLocation();

        if ($form->isValid() && $form->isSubmitted()) {
            $contentInfo = $location->getContentInfo();
            $parentLocation = $this->locationService->loadLocation($location->parentLocationId);
            $this->trashService->trash($location);

            $this->flashSuccess('location.trash.success', [
                '%locationName%' => $contentInfo->name,
            ], 'location');

            return $this->redirectToRoute($parentLocation);
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
    }

    /**
     * Handles removing locations assigned to content item based on submitted form.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function removeAction(Request $request): Response
    {
        $form = $this->formFactory->removeLocation();
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();
        /** @var ContentLocationRemoveData $contentLocationRemoveData */
        $contentLocationRemoveData = $uiFormData->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @todo empty locations should be validated on form level */
            foreach ($contentLocationRemoveData->getLocations() as $locationId => $selected) {
                $location = $this->locationService->loadLocation($locationId);
                $this->locationService->deleteLocation($location);
            }

            $this->flashSuccess('location.remove.success', [
                '%locationName%' => $contentLocationRemoveData->getContentInfo()->name,
            ], 'location');

            return $this->redirect($uiFormData->getOnSuccessRedirectionUrl());
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
    }

    /**
     * Handles assigning new location to the content item based on submitted form.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->formFactory->addLocation();
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();
        /** @var ContentLocationAddData $contentLocationAddData */
        $contentLocationAddData = $uiFormData->getData();

        $contentInfo = $contentLocationAddData->getContentInfo();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @todo empty locations should be validated on form level */
            foreach ($contentLocationAddData->getNewLocations() as $newLocation) {
                $locationCreateStruct = $this->locationService->newLocationCreateStruct($newLocation->id);
                $this->locationService->createLocation($contentInfo, $locationCreateStruct);
            }

            $this->flashSuccess(
                'location.add.success',
                ['%locationName%' => $contentInfo->name],
                'location'
            );

            return $this->redirect($uiFormData->getOnSuccessRedirectionUrl());
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
    }
}
