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
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemDeleteData;
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
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\API\Repository\TrashService */
    private $trashService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\PathService */
    private $uiPathService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\TrashService $trashService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \EzSystems\EzPlatformAdminUi\UI\Service\PathService $uiPathService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
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
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
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
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
    }

    public function performAccessCheck(): void
    {
        parent::performAccessCheck();
        $this->denyAccessUnlessGranted(new Attribute('content', 'restore'));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @internal param int $page Current page
     * @internal param int $limit Number of items per page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerMaxPerPageException
     * @throws \Pagerfanta\Exception\LessThan1MaxPerPageException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
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

        /** @var \eZ\Publish\API\Repository\Values\Content\TrashItem $item */
        foreach ($pagerfanta->getCurrentPageResults() as $item) {
            $contentType = $this->contentTypeService->loadContentType(
                $item->getContentInfo()->contentTypeId,
                $this->userLanguagePreferenceProvider->getPreferredLanguages()
            );
            $ancestors = $this->uiPathService->loadPathLocations($item);

            $trashItemsList[] = new TrashItemData($item, $contentType, $ancestors);
        }

        $trashItemRestoreForm = $this->formFactory->restoreTrashItem(
            new TrashItemRestoreData($pagerfanta->getCurrentPageResults(), null)
        );

        $trashItemDeleteForm = $this->formFactory->deleteTrashItem(
            new TrashItemDeleteData($pagerfanta->getCurrentPageResults())
        );

        $trashEmptyForm = $this->formFactory->emptyTrash(
            new TrashEmptyData(true)
        );

        return $this->render('@ezdesign/admin/trash/list.html.twig', [
            'can_delete' => $this->isGranted(new Attribute('content', 'remove')),
            'can_restore' => $this->isGranted(new Attribute('content', 'restore')),
            'can_cleantrash' => $this->isGranted(new Attribute('content', 'cleantrash')),
            'trash_items' => $trashItemsList,
            'pager' => $pagerfanta,
            'form_trash_item_restore' => $trashItemRestoreForm->createView(),
            'form_trash_item_delete' => $trashItemDeleteForm->createView(),
            'form_trash_empty' => $trashEmptyForm->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function emptyAction(Request $request): Response
    {
        if (!$this->isGranted(new Attribute('content', 'cleantrash'))) {
            return $this->redirect($this->generateUrl('ezplatform.trash.list'));
        }

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

        return $this->redirect($this->generateUrl('ezplatform.trash.list'));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function restoreAction(Request $request): Response
    {
        if (!$this->isGranted(new Attribute('content', 'restore'))) {
            return $this->redirect($this->generateUrl('ezplatform.trash.list'));
        }

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
                            /** @Desc("Items restored under '%location%' location.") */
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

        return $this->redirect($this->generateUrl('ezplatform.trash.list'));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function deleteAction(Request $request): Response
    {
        if (!$this->isGranted(new Attribute('content', 'remove'))) {
            return $this->redirect($this->generateUrl('ezplatform.trash.list'));
        }

        $form = $this->formFactory->deleteTrashItem();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TrashItemDeleteData $data) {
                foreach ($data->getTrashItems() as $trashItem) {
                    $this->trashService->deleteTrashItem($trashItem);
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Deleted selected trash items.") */
                        'trash.deleted.success',
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

        return $this->redirect($this->generateUrl('ezplatform.trash.list'));
    }
}
