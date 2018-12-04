<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\Values\Content\LocationUpdateStruct;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopySubtreeData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashContainerData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\DetailsTab;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\LocationsTab;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException as APIRepositoryUnauthorizedException;

class LocationController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\TrashService */
    private $trashService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\TrashService $trashService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        TrashService $trashService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        PermissionResolver $permissionResolver
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->trashService = $trashService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveAction(Request $request): Response
    {
        $form = $this->formFactory->moveLocation(
            new LocationMoveData()
        );
        $form->handleRequest($request);

        $location = $form->getData()->getLocation();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LocationMoveData $data) {
                $location = $data->getLocation();
                $newParentLocation = $data->getNewParentLocation();

                if (!$newParentLocation->getContent()->getContentType()->isContainer) {
                    throw new InvalidArgumentException(
                        '$newParentLocation',
                        'Cannot move location to a parent that is not a container'
                    );
                }

                $this->locationService->moveSubtree($location, $newParentLocation);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("'%name%' moved to '%location%'") */
                        'location.move.success',
                        ['%name%' => $location->getContentInfo()->name, '%location%' => $newParentLocation->getContentInfo()->name],
                        'location'
                    )
                );

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $location->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $location->id,
        ]));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function copyAction(Request $request): Response
    {
        $form = $this->formFactory->copyLocation(
            new LocationCopyData()
        );
        $form->handleRequest($request);

        $location = $form->getData()->getLocation();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LocationCopyData $data) {
                $location = $data->getLocation();
                $newParentLocation = $data->getNewParentLocation();

                if (!$newParentLocation->getContent()->getContentType()->isContainer) {
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

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("'%name%' copied to '%location%'") */
                        'location.copy.success',
                        ['%name%' => $location->getContentInfo()->name, '%location%' => $newParentLocation->getContentInfo()->name],
                        'location'
                    )
                );

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $newLocation->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $location->id,
        ]));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function copySubtreeAction(Request $request): Response
    {
        $form = $this->formFactory->copyLocationSubtree(
            new LocationCopySubtreeData()
        );
        $form->handleRequest($request);

        $location = $form->getData()->getLocation();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LocationCopySubtreeData $data) use ($location) {
                $newParentLocation = $data->getNewParentLocation();

                $copiedContent = $this->locationService->copySubtree(
                    $location,
                    $newParentLocation
                );

                $newLocation = $this->locationService->loadLocation($copiedContent->contentInfo->mainLocationId);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Subtree '%name%' copied to location '%location%'") */
                        'location.copy_subtree.success', [
                        '%name%' => $location->getContentInfo()->name,
                        '%location%' => $newParentLocation->getContentInfo()->name,
                    ],
                        'location'
                    )
                );

                return $this->redirectToLocation($newLocation);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToLocation($location);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function swapAction(Request $request): Response
    {
        $form = $this->formFactory->swapLocation(
            new LocationSwapData()
        );
        $form->handleRequest($request);

        $location = $form->getData()->getCurrentLocation();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LocationSwapData $data) {
                $currentLocation = $data->getCurrentLocation();
                $newLocation = $data->getNewLocation();

                $childCount = $this->locationService->getLocationChildCount($currentLocation);
                $contentType = $newLocation->getContent()->getContentType();

                if (!$contentType->isContainer && $childCount) {
                    throw new \InvalidArgumentException(
                        'Cannot swap location that has sub items with a location that is not a container'
                    );
                }
                $this->locationService->swapLocation($currentLocation, $newLocation);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Location '%name%' swaped with location '%location%'") */
                        'location.swap.success',
                        ['%name%' => $currentLocation->getContentInfo()->name, '%location%' => $newLocation->getContentInfo()->name],
                        'location'
                    )
                );

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $newLocation->id,
                    '_fragment' => LocationsTab::URI_FRAGMENT,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $location->id,
            '_fragment' => LocationsTab::URI_FRAGMENT,
        ]));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trashAction(Request $request): Response
    {
        $form = $this->formFactory->trashLocation(
            new LocationTrashData()
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LocationTrashData $data) {
                return $this->handleTrashLocationForm($data);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.trash.list'));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trashContainerAction(Request $request): Response
    {
        $form = $this->formFactory->trashContainerLocation(
            new LocationTrashContainerData()
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LocationTrashContainerData $data) {
                return $this->handleTrashLocationForm($data);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.trash.list'));
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData|\EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashContainerData $data
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function handleTrashLocationForm($data): RedirectResponse
    {
        $location = $data->getLocation();
        $parentLocation = $this->locationService->loadLocation($location->parentLocationId);
        $this->trashService->trash($location);

        $this->notificationHandler->success(
            $this->translator->trans(
                /** @Desc("Location '%name%' moved to trash.") */
                'location.trash.success',
                ['%name%' => $location->getContentInfo()->name],
                'location'
            )
        );

        return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
            'locationId' => $parentLocation->id,
        ]));
    }

    /**
     * Handles removing locations assigned to content item based on submitted form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request): Response
    {
        $form = $this->formFactory->removeLocation(
            new ContentLocationRemoveData()
        );
        $form->handleRequest($request);

        /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
        $contentInfo = $form->getData()->getContentInfo();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentLocationRemoveData $data) {
                $contentInfo = $data->getContentInfo();

                foreach ($data->getLocations() as $locationId => $selected) {
                    $location = $this->locationService->loadLocation($locationId);
                    $this->locationService->deleteLocation($location);

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Location '%name%' removed.") */
                            'location.delete.success',
                            ['%name%' => $location->getContentInfo()->name],
                            'location'
                        )
                    );
                }

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $contentInfo->mainLocationId,
                    '_fragment' => LocationsTab::URI_FRAGMENT,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $contentInfo->mainLocationId,
            '_fragment' => LocationsTab::URI_FRAGMENT,
        ]));
    }

    /**
     * Handles assigning new location to the content item based on submitted form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->formFactory->addLocation(
            new ContentLocationAddData()
        );
        $form->handleRequest($request);

        $contentInfo = $form->getData()->getContentInfo();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentLocationAddData $data) {
                $contentInfo = $data->getContentInfo();

                foreach ($data->getNewLocations() as $newLocation) {
                    $locationCreateStruct = $this->locationService->newLocationCreateStruct($newLocation->id);
                    $this->locationService->createLocation($contentInfo, $locationCreateStruct);

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Location '%name%' created.") */
                            'location.create.success',
                            ['%name%' => $newLocation->getContentInfo()->name],
                            'location'
                        )
                    );
                }

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $contentInfo->mainLocationId,
                    '_fragment' => LocationsTab::URI_FRAGMENT,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $contentInfo->mainLocationId,
            '_fragment' => LocationsTab::URI_FRAGMENT,
        ]));
    }

    /**
     * Handles toggling visibility location of a content item based on submitted form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateVisibilityAction(Request $request): Response
    {
        $form = $this->formFactory->updateVisibilityLocation();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $data->getLocation();
            $hidden = $data->getHidden();

            try {
                /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
                $contentInfo = $location->getContentInfo();

                if ($hidden) {
                    $this->locationService->hideLocation($location);
                    $message = $this->translator->trans(
                        /** @Desc("Location '%name%' hidden.") */
                        'location.update_success.success.hidden',
                        ['%name%' => $contentInfo->name],
                        'location'
                    );
                } else {
                    $this->locationService->unhideLocation($location);
                    $message = $this->translator->trans(
                        /** @Desc("Location '%name%' unhidden.") */
                        'location.update_success.success.unhidden',
                        ['%name%' => $contentInfo->name],
                        'location'
                    );
                }
            } catch (APIRepositoryUnauthorizedException $e) {
                return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_UNAUTHORIZED);
            }
        } else {
            $errors = [];
            foreach ($form->getErrors(true, true) as $formError) {
                $errors[] = $formError->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(['message' => $message]);
    }

    /**
     * Handles update existing location.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request): Response
    {
        $form = $this->formFactory->updateLocation();
        $form->handleRequest($request);

        $location = $form->getData()->getLocation();

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (LocationUpdateData $data) {
                $location = $data->getLocation();

                $locationUpdateStruct = new LocationUpdateStruct(['sortField' => $data->getSortField(), 'sortOrder' => $data->getSortOrder()]);
                $this->locationService->updateLocation($location, $locationUpdateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Location '%name%' updated.") */
                        'location.update.success',
                        ['%name%' => $location->getContentInfo()->name],
                        'location'
                    )
                );

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                        'locationId' => $location->getContentInfo()->mainLocationId,
                        '_fragment' => DetailsTab::URI_FRAGMENT,
                    ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $location->getContentInfo()->mainLocationId,
            '_fragment' => DetailsTab::URI_FRAGMENT,
        ]));
    }
}
