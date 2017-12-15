<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\URL\Query\Criterion as Criterion;
use eZ\Publish\API\Repository\Values\URL\Query\SortClause as SortClause;
use eZ\Publish\API\Repository\Values\URL\URL;
use eZ\Publish\API\Repository\Values\URL\URLQuery;
use EzSystems\RepositoryForms\Data\URL\URLListData;
use EzSystems\RepositoryForms\Data\URL\URLUpdateData;
use EzSystems\RepositoryForms\Pagination\Pagerfanta\URLSearchAdapter;
use EzSystems\RepositoryForms\Pagination\Pagerfanta\URLUsagesAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Translation\TranslatorInterface;

class LinkManagerController extends Controller
{
    const DEFAULT_MAX_PER_PAGE = 10;

    /** @var URLService */
    private $urlService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * EzPlatformLinkManagerController constructor.
     *
     * @param URLService $urlService
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     */
    public function __construct(
        URLService $urlService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator)
    {
        $this->urlService = $urlService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
    }

    /**
     * Renders the URLs list.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $data = new URLListData();

        $form = $this->formFactory->createUrlListForm($data, '', [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $urls = new Pagerfanta(new URLSearchAdapter(
            $this->buildListQuery($data),
            $this->urlService
        ));

        $urls->setCurrentPage($data->page);
        $urls->setMaxPerPage($data->limit ? $data->limit : self::DEFAULT_MAX_PER_PAGE);

        return $this->render('@EzPlatformAdminUi/link_manager/list.html.twig', [
            'form' => $form->createView(),
            'can_edit' => $this->isGranted(new Attribute('url', 'update')),
            'urls' => $urls,
        ]);
    }

    /**
     * Displays the edit form and processes it once submitted.
     *
     * @param int $urlId ID of URL
     *
     * @return Response
     */
    public function editAction(Request $request, int $urlId): Response
    {
        $url = $this->urlService->loadById($urlId);

        $form = $this->formFactory->createUrlEditForm(new URLUpdateData([
            'id' => $url->id,
            'url' => $url->url,
        ]));

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (URLUpdateData $data) use ($url) {
                $this->urlService->updateUrl($url, $data);
                $this->notificationHandler->success(
                    $this->translator->trans('url.update.success', [], 'linkmanager')
                );

                return $this->redirectToRoute('ezplatform.link_manager.list');
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/link_manager/edit.html.twig', [
            'form' => $form->createView(),
            'url' => $url,
        ]);
    }

    /**
     * Renders the view of a URL.
     *
     * @param Request $request
     * @param int $urlId ID of URL
     *
     * @return Response
     */
    public function viewAction(Request $request, int $urlId): Response
    {
        $url = $this->urlService->loadById($urlId);

        $usages = new Pagerfanta(new URLUsagesAdapter($url, $this->urlService));
        $usages->setCurrentPage($request->query->getInt('page', 1));
        $usages->setMaxPerPage($request->query->getInt('limit', self::DEFAULT_MAX_PER_PAGE));

        return $this->render('@EzPlatformAdminUi/link_manager/view.html.twig', [
            'url' => $url,
            'can_edit' => $this->isGranted(new Attribute('url', 'update')),
            'usages' => $usages,
        ]);
    }

    /**
     * Builds URL criteria from list data.
     *
     * @param URLListData $data
     *
     * @return URLQuery
     */
    private function buildListQuery(URLListData $data): URLQuery
    {
        $query = new URLQuery();
        $query->sortClauses = [
            new SortClause\URL(),
        ];

        $criteria = [
            new Criterion\VisibleOnly(),
        ];

        if ($data->searchQuery !== null) {
            $criteria[] = new Criterion\Pattern($data->searchQuery);
        }

        if ($data->status !== null) {
            $criteria[] = new Criterion\Validity($data->status);
        }

        $query->filter = new Criterion\LogicalAnd($criteria);

        return $query;
    }
}
