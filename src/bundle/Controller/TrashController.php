<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Data\UiFormData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Service\TrashService as UiTrashService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class TrashController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var UiTrashService */
    private $uiTrashService;

    /** @var TrashService */
    private $trashService;

    /** @var LocationService */
    private $locationService;

    /** @var ContentService */
    private $contentService;

    /** @var FormFactory */
    private $formFactory;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param UiTrashService $uiTrashService
     * @param TrashService $trashService
     * @param LocationService $locationService
     * @param ContentService $contentService
     * @param FormFactory $formFactory
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        UiTrashService $uiTrashService,
        TrashService $trashService,
        LocationService $locationService,
        ContentService $contentService,
        FormFactory $formFactory
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->uiTrashService = $uiTrashService;
        $this->trashService = $trashService;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
    }

    public function listAction(): Response
    {
        $trashItemsList = $this->uiTrashService->loadTrashItems();
        $trashItemRestoreData = new TrashItemRestoreData($trashItemsList, null);
        $trashListUrl = $this->generateUrl('ezplatform.trash.list');
        $trashItemRestoreForm = $this->formFactory->restoreTrashItem(
            null,
            $trashItemRestoreData,
            $trashListUrl
        );
        $trashEmptyForm = $this->formFactory->emptyTrash(
            null,
            new TrashEmptyData(true),
            $trashListUrl,
            $trashListUrl
        );

        return $this->render('@EzPlatformAdminUi/admin/trash/list.html.twig', [
            'can_delete' => $this->isGranted(new Attribute('content', 'remove')),
            'can_restore' => $this->isGranted(new Attribute('content', 'restore')),
            'trash_items' => $trashItemsList,
            'form_trash_item_restore' => $trashItemRestoreForm->createView(),
            'form_trash_empty' => $trashEmptyForm->createView(),
        ]);
    }

    public function emptyAction(Request $request): Response
    {
        /** @todo Can permissions be checked on form level? */
        if ($this->isGranted(new Attribute('content', 'remove'))) {
            $emptyTrashForm = $this->formFactory->emptyTrash();
            $emptyTrashForm->handleRequest($request);

            /** @var UiFormData $uiFormData */
            $uiFormData = $emptyTrashForm->getData();

            if ($emptyTrashForm->isValid() && $emptyTrashForm->isSubmitted()) {
                $this->trashService->emptyTrash();

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Trash empty.") */ 'trash.empty.success',
                        [],
                        'trash'
                    )
                );

                return $this->redirect($uiFormData->getOnSuccessRedirectionUrl());
            }

            /**
             * @todo We should implement a service for converting form errors into notifications
             */
            foreach ($emptyTrashForm->getErrors(true, true) as $formError) {
                $this->notificationHandler->error($formError->getMessage());
            }

            return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
        }

        return $this->redirectToRoute('ezplatform.trash.list');
    }

    public function restoreAction(Request $request): Response
    {
        /** @todo Can permissions be checked on form level? */
        if ($this->isGranted(new Attribute('content', 'restore'))) {
            $restoreTrashItemForm = $this->formFactory->restoreTrashItem();
            $restoreTrashItemForm->handleRequest($request);

            /** @var UiFormData $uiFormData */
            $uiFormData = $restoreTrashItemForm->getData();
            if ($restoreTrashItemForm->isValid() && $restoreTrashItemForm->isSubmitted()) {
                /** @var TrashItemRestoreData $trashItemRestoreData */
                $trashItemRestoreData = $uiFormData->getData();
                $newParentLocation = $trashItemRestoreData->getLocation();

                foreach ($trashItemRestoreData->getTrashItems() as $trashItem) {
                    $this->trashService->recover($trashItem->getLocation(), $newParentLocation);
                }

                if (null === $newParentLocation) {
                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Items restored under their original location.") */ 'trash.restore_original_location.success',
                            [],
                            'trash'
                        )
                    );
                } else {
                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Items restored under `%location%` location.") */ 'trash.restore_new_location.success',
                            ['%location%' => $newParentLocation->getContentInfo()->name],
                            'trash'
                        )
                    );
                }

                return $this->redirect($uiFormData->getOnSuccessRedirectionUrl());
            }

            /**
             * @todo We should implement a service for converting form errors into notifications
             */
            foreach ($restoreTrashItemForm->getErrors(true, true) as $formError) {
                $this->notificationHandler->error($formError->getMessage());
            }

            return $this->redirect($uiFormData->getOnFailureRedirectionUrl());
        }

        return $this->redirectToRoute('ezplatform.trash.list');
    }
}
