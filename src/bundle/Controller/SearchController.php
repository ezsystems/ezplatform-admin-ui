<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use eZ\Publish\API\Repository\SearchService;
use EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Tab\Dashboard\PagerContentToDataMapper;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchController extends Controller
{
    /** @var SearchService */
    private $searchService;

    /** @var PagerContentToDataMapper */
    private $pagerContentToDataMapper;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param SearchService $searchService
     * @param PagerContentToDataMapper $pagerContentToDataMapper
     * @param UrlGeneratorInterface $urlGenerator
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        SearchService $searchService,
        PagerContentToDataMapper $pagerContentToDataMapper,
        UrlGeneratorInterface $urlGenerator,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        int $defaultPaginationLimit
    ) {
        $this->searchService = $searchService;
        $this->pagerContentToDataMapper = $pagerContentToDataMapper;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * Renders the simple search form and search results.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     */
    public function searchAction(Request $request): Response
    {
        $search = $request->query->get('search');
        $limit = $search['limit'] ?? $this->defaultPaginationLimit;
        $page = $search['page'] ?? 1;
        $query = $search['query'];

        $form = $this->formFactory->createSearchForm(
            new SearchData($limit, $page, $query),
            'search',
            [
                'method' => Request::METHOD_GET,
                'csrf_protection' => false,
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (SearchData $data) use ($form) {
                $limit = $data->getLimit();
                $page = $data->getPage();
                $queryString = $data->getQuery();

                $query = new Query();
                $query->filter = new Criterion\LogicalAnd(
                    [
                        new Criterion\Visibility(Criterion\Visibility::VISIBLE),
                        new Criterion\FullText($queryString),
                    ]
                );

                $query->sortClauses[] = new SortClause\DateModified(Query::SORT_ASC);

                $pagerfanta = new Pagerfanta(
                    new ContentSearchAdapter($query, $this->searchService)
                );

                $pagerfanta->setMaxPerPage($limit);
                $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

                return $this->render('@EzPlatformAdminUi/admin/search/search.html.twig', [
                    'results' => $this->pagerContentToDataMapper->map($pagerfanta),
                    'form' => $form->createView(),
                    'pager' => $pagerfanta,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/search/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
