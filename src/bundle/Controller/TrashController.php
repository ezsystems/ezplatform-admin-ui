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
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService as UiPathService;

class TrashController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var TrashService */
    private $trashService;

    /** @var LocationService */
    private $locationService;

    /** @var ContentService */
    private $contentService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var UiPathService */
    private $uiPathService;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param TrashService $trashService
     * @param LocationService $locationService
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     * @param UiPathService $uiPathService
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        TrashService $trashService,
        LocationService $locationService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        UiPathService $uiPathService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->trashService = $trashService;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->uiPathService = $uiPathService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    /**
     * @return Response
     */
    public function listAction(): Response
    {
        $trashListUrl = $this->generateUrl('ezplatform.trash.list');

        $trashItemsList = [];
        $trashItems = $this->trashService->findTrashItems(new Query([
            'sortClauses' => [new Query\SortClause\Location\Priority(Query::SORT_ASC)],
        ]));
        foreach ($trashItems->items as $item) {
            $contentType = $this->contentTypeService->getContentType($item->contentInfo->contentTypeId);
            $ancestors = $this->uiPathService->loadPathLocations($item);

            $trashItemsList[] = new TrashItemData($item, $contentType, $ancestors);
        }

        $trashItemRestoreForm = $this->formFactory->restoreTrashItem(
            new TrashItemRestoreData($trashItemsList, null),
            $trashListUrl,
            $trashListUrl
        );
        $trashEmptyForm = $this->formFactory->emptyTrash(
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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function emptyAction(Request $request): Response
    {
        $trashListUrl = $this->generateUrl('ezplatform.trash.list');

        if ($this->isGranted(new Attribute('content', 'remove'))) {
            $form = $this->formFactory->emptyTrash(
                new TrashEmptyData(true),
                $trashListUrl,
                $trashListUrl
            );
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $result = $this->submitHandler->handle($form, function () {
                    $this->trashService->emptyTrash();

                    $this->notificationHandler->success(
                        $this->translator->trans(
/** @Desc("Trash empty.") */ 'trash.empty.success',
                            [],
                            'trash'
                        )
                    );
                });

                if ($result instanceof Response) {
                    return $result;
                }
            }
        }

        /* Fallback Redirect */
        return $this->redirect($trashListUrl);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function restoreAction(Request $request): Response
    {
        $trashListUrl = $this->generateUrl('ezplatform.trash.list');

        if ($this->isGranted(new Attribute('content', 'restore'))) {
            $form = $this->formFactory->restoreTrashItem(
                new TrashItemRestoreData(),
                $trashListUrl,
                $trashListUrl
            );
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $result = $this->submitHandler->handle($form, function (TrashItemRestoreData $data) {
                    $newParentLocation = $data->getLocation();

                    foreach ($data->getTrashItems() as $trashItem) {
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
                });

                if ($result instanceof Response) {
                    return $result;
                }
            }
        }

        /* Fallback Redirect */
        return $this->redirect($trashListUrl);
    }
}
