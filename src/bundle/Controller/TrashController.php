<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\TrashItem;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Search\TrashSearchData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
use EzSystems\EzPlatformAdminUi\Form\Factory\TrashFormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Form\Type\Search\TrashSearchType;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\TrashItemAdapter;
use EzSystems\EzPlatformAdminUi\QueryType\TrashSearchQueryType;
use EzSystems\EzPlatformAdminUi\Specification\UserExists;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService as UiPathService;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrashController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\TrashService */
    private $trashService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\TrashFormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\PathService */
    private $uiPathService;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \EzSystems\EzPlatformAdminUi\QueryType\TrashSearchQueryType */
    private $trashSearchQueryType;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        TrashService $trashService,
        ContentTypeService $contentTypeService,
        UiPathService $uiPathService,
        TrashFormFactory $formFactory,
        SubmitHandler $submitHandler,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        ConfigResolverInterface $configResolver,
        TrashSearchQueryType $trashSearchQueryType,
        UserService $userService
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->trashService = $trashService;
        $this->contentTypeService = $contentTypeService;
        $this->uiPathService = $uiPathService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->configResolver = $configResolver;
        $this->trashSearchQueryType = $trashSearchQueryType;
        $this->userService = $userService;
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
        $searchFormName = StringUtil::fqcnToBlockPrefix(TrashSearchType::class);
        $searchForm = $this->formFactory->searchTrash(new TrashSearchData());

        $searchForm->handleRequest($request);

        $requestedPage = $request->query->get($searchFormName)['page'] ?? null;
        $page = empty($requestedPage) ? 1 : (int)$requestedPage;
        $trashItemsList = [];

        $pagerfanta = new Pagerfanta(
            new TrashItemAdapter(
                $this->trashSearchQueryType->getQuery([
                    'search_data' => $searchForm->getData(),
                ]),
                $this->trashService
            )
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination.trash_limit'));
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var \eZ\Publish\API\Repository\Values\Content\TrashItem $item */
        foreach ($pagerfanta->getCurrentPageResults() as $item) {
            $contentType = $this->contentTypeService->loadContentType(
                $item->getContentInfo()->contentTypeId,
                $this->userLanguagePreferenceProvider->getPreferredLanguages()
            );
            $ancestors = $this->uiPathService->loadPathLocations($item);
            $creator = $this->getCreatorFromTrashItem($item);

            $trashItemsList[] = new TrashItemData($item, $contentType, $ancestors, $creator);
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

        return $this->render('@ezdesign/trash/list.html.twig', [
            'can_delete' => $this->isGranted(new Attribute('content', 'remove')),
            'can_restore' => $this->isGranted(new Attribute('content', 'restore')),
            'can_cleantrash' => $this->isGranted(new Attribute('content', 'cleantrash')),
            'trash_items' => $trashItemsList,
            'pager' => $pagerfanta,
            'form_trash_item_restore' => $trashItemRestoreForm->createView(),
            'form_trash_item_delete' => $trashItemDeleteForm->createView(),
            'form_trash_empty' => $trashEmptyForm->createView(),
            'form_search' => $searchForm->createView(),
            'user_content_type_identifier' => $this->configResolver->getParameter('user_content_type_identifier'),
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
                    /** @Desc("Trash emptied.") */
                    'trash.empty.success',
                    [],
                    'trash'
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
            return $this->redirectToTrashList($request);
        }

        $form = $this->formFactory->restoreTrashItem();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (TrashItemRestoreData $data) use ($request) {
                    $newParentLocation = $data->getLocation();

                    foreach ($data->getTrashItems() as $trashItem) {
                        $this->trashService->recover($trashItem, $newParentLocation);
                    }

                    if (null === $newParentLocation) {
                        $this->notificationHandler->success(
                            /** @Desc("Restored content to its original Location.") */
                            'trash.restore_original_location.success',
                            [],
                            'trash'
                        );
                    } else {
                        $this->notificationHandler->success(
                            /** @Desc("Restored content under Location '%location%'.") */
                            'trash.restore_new_location.success',
                            ['%location%' => $newParentLocation->getContentInfo()->name],
                            'trash'
                        );
                    }

                    return $this->redirectToTrashList($request);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToTrashList($request);
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
            return $this->redirectToTrashList($request);
        }

        $form = $this->formFactory->deleteTrashItem();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (TrashItemDeleteData $data) use ($request) {
                    foreach ($data->getTrashItems() as $trashItem) {
                        $this->trashService->deleteTrashItem($trashItem);
                    }

                    $this->notificationHandler->success(
                        /** @Desc("Deleted selected item(s) from Trash.") */
                        'trash.deleted.success',
                        [],
                        'trash'
                    );

                    return $this->redirectToTrashList($request);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToTrashList($request);
    }

    private function redirectToTrashList(Request $request): RedirectResponse
    {
        $trashSearchParams = $request->get('trash_search');
        $params = $trashSearchParams ? ['trash_search' => $trashSearchParams] : [];

        return $this->redirect(
            $this->generateUrl('ezplatform.trash.list', $params)
        );
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function getCreatorFromTrashItem(TrashItem $trashItem): ?User
    {
        $ownerId = $trashItem->getContentInfo()->ownerId;

        if (false === (new UserExists($this->userService))->isSatisfiedBy($ownerId)) {
            return null;
        }

        return $this->userService->loadUser($trashItem->getContentInfo()->ownerId);
    }
}
