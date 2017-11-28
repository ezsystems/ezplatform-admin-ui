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
use eZ\Publish\API\Repository\Values\Content\TrashItem;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\TrashItemAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var int */
    private $defaultPaginationLimit;

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
     * @param UrlGeneratorInterface $urlGenerator
     * @param int $defaultPaginationLimit
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
        SubmitHandler $submitHandler,
        UrlGeneratorInterface $urlGenerator,
        int $defaultPaginationLimit
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
        $this->urlGenerator = $urlGenerator;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * @param int $page Current page
     * @param int $limit Number of items per page
     *
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page') ?? 1;
        $trashItemsList = [];

        $query = new Query([
            'sortClauses' => [new Query\SortClause\Location\Priority(Query::SORT_ASC)],
        ]);

        $pagerfanta = new Pagerfanta(
            new TrashItemAdapter($query, $this->trashService)
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var TrashItem $item */
        foreach ($pagerfanta->getCurrentPageResults() as $item) {
            $contentType = $this->contentTypeService->loadContentType($item->contentInfo->contentTypeId);
            $ancestors = $this->uiPathService->loadPathLocations($item);

            $trashItemsList[] = new TrashItemData($item, $contentType, $ancestors);
        }

        $trashItemRestoreForm = $this->formFactory->restoreTrashItem(
            new TrashItemRestoreData($pagerfanta->getCurrentPageResults(), null)
        );

        $trashEmptyForm = $this->formFactory->emptyTrash(
            new TrashEmptyData(true)
        );

        return $this->render('@EzPlatformAdminUi/admin/trash/list.html.twig', [
            'can_delete' => $this->isGranted(new Attribute('content', 'remove')),
            'can_restore' => $this->isGranted(new Attribute('content', 'restore')),
            'trash_items' => $trashItemsList,
            'pager' => $pagerfanta,
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
        if ($this->isGranted(new Attribute('content', 'remove'))) {
            $form = $this->formFactory->emptyTrash(
                new TrashEmptyData(true)
            );
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $result = $this->submitHandler->handle($form, function () {
                    $this->trashService->emptyTrash();

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Trash empty.") */
                            'trash.empty.success',
                            [],
                            'trash'
                        )
                    );

                    return new RedirectResponse($this->generateUrl('ezplatform.trash.list'));
                });

                if ($result instanceof Response) {
                    return $result;
                }
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.trash.list'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function restoreAction(Request $request): Response
    {
        if ($this->isGranted(new Attribute('content', 'restore'))) {
            $form = $this->formFactory->restoreTrashItem();
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $result = $this->submitHandler->handle($form, function (TrashItemRestoreData $data) {
                    $newParentLocation = $data->getLocation();

                    foreach ($data->getTrashItems() as $trashItem) {
                        $this->trashService->recover($trashItem, $newParentLocation);
                    }

                    if (null === $newParentLocation) {
                        $this->notificationHandler->success(
                            $this->translator->trans(
                                /** @Desc("Items restored under their original location.") */
                                'trash.restore_original_location.success',
                                [],
                                'trash'
                            )
                        );
                    } else {
                        $this->notificationHandler->success(
                            $this->translator->trans(
                                /** @Desc("Items restored under `%location%` location.") */
                                'trash.restore_new_location.success',
                                ['%location%' => $newParentLocation->getContentInfo()->name],
                                'trash'
                            )
                        );
                    }

                    return new RedirectResponse($this->generateUrl('ezplatform.trash.list'));
                });

                if ($result instanceof Response) {
                    return $result;
                }
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.trash.list'));
    }
}
