<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\AdminUi\Controller;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Ibexa\AdminUi\Form\Data\Bookmark\BookmarkRemoveData;
use Ibexa\AdminUi\Form\Data\Content\Draft\ContentEditData;
use Ibexa\AdminUi\Form\Factory\FormFactory;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\AdminUi\Pagination\Pagerfanta\BookmarkAdapter;
use Ibexa\AdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ibexa\Contracts\AdminUi\Controller\Controller;

class BookmarkController extends Controller
{
    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    private $datasetFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        BookmarkService $bookmarkService,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        LocationService $locationService,
        SubmitHandler $submitHandler,
        ConfigResolverInterface $configResolver
    ) {
        $this->bookmarkService = $bookmarkService;
        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->locationService = $locationService;
        $this->submitHandler = $submitHandler;
        $this->configResolver = $configResolver;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page', 1);

        $pagerfanta = new Pagerfanta(
            new BookmarkAdapter($this->bookmarkService, $this->datasetFactory)
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination.bookmark_limit'));
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        $editForm = $this->formFactory->contentEdit(
            new ContentEditData()
        );

        $removeBookmarkForm = $this->formFactory->removeBookmark(
            new BookmarkRemoveData($this->getChoices($pagerfanta->getCurrentPageResults()))
        );

        return $this->render(
            '@ezdesign/account/bookmarks/list.html.twig',
            $viewParameters = [
                'pager' => $pagerfanta,
                'form_edit' => $editForm->createView(),
                'form_remove' => $removeBookmarkForm->createView(),
            ]
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request): Response
    {
        $form = $this->formFactory->removeBookmark(
            new BookmarkRemoveData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (BookmarkRemoveData $data) {
                foreach ($data->getBookmarks() as $locationId => $selected) {
                    $this->bookmarkService->deleteBookmark(
                        $this->locationService->loadLocation($locationId)
                    );
                }

                return $this->redirectToRoute('ezplatform.bookmark.list');
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ezplatform.bookmark.list');
    }

    /**
     * @param array $bookmarks
     *
     * @return array
     */
    private function getChoices(array $bookmarks): array
    {
        $bookmarks = array_column($bookmarks, 'id');

        return array_combine($bookmarks, array_fill_keys($bookmarks, false));
    }
}

class_alias(BookmarkController::class, 'EzSystems\EzPlatformAdminUiBundle\Controller\BookmarkController');
