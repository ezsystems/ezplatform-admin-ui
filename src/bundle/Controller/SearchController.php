<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
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
    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \EzSystems\EzPlatformAdminUi\Tab\Dashboard\PagerContentToDataMapper */
    private $pagerContentToDataMapper;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\API\Repository\SectionService */
    private $sectionService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var int */
    private $defaultPaginationLimit;

    /** @var array */
    private $userContentTypeIdentifier;

    /**
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \EzSystems\EzPlatformAdminUi\Tab\Dashboard\PagerContentToDataMapper $pagerContentToDataMapper
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param \eZ\Publish\API\Repository\SectionService $sectionService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param int $defaultPaginationLimit
     * @param array $userContentTypeIdentifier
     */
    public function __construct(
        SearchService $searchService,
        PagerContentToDataMapper $pagerContentToDataMapper,
        UrlGeneratorInterface $urlGenerator,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        SectionService $sectionService,
        ContentTypeService $contentTypeService,
        int $defaultPaginationLimit,
        array $userContentTypeIdentifier
    ) {
        $this->searchService = $searchService;
        $this->pagerContentToDataMapper = $pagerContentToDataMapper;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->sectionService = $sectionService;
        $this->contentTypeService = $contentTypeService;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
    }

    /**
     * Renders the simple search form and search results.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \InvalidArgumentException
     */
    public function searchAction(Request $request): Response
    {
        $search = $request->query->get('search');
        $limit = $search['limit'] ?? $this->defaultPaginationLimit;
        $page = $search['page'] ?? 1;
        $query = $search['query'];
        $section = null;
        $creator = null;
        $contentTypes = [];
        $lastModified = $search['last_modified'] ?? [];
        $created = $search['created'] ?? [];
        $subtree = $search['subtree'] ?? null;
        $searchLanguage = null;
        $useAlwaysAvailable = $search['use_always_available'] ? (bool) $search['use_always_available'] : null;

        if (!empty($search['section'])) {
            $section = $this->sectionService->loadSection($search['section']);
        }
        if (!empty($search['content_types']) && is_array($search['content_types'])) {
            foreach ($search['content_types'] as $identifier) {
                $contentTypes[] = $this->contentTypeService->loadContentTypeByIdentifier($identifier);
            }
        }

        $form = $this->formFactory->createSearchForm(
            new SearchData(
                $limit,
                $page,
                $query,
                $section,
                $contentTypes,
                $lastModified,
                $created,
                $creator,
                $subtree,
                $searchLanguage,
                $useAlwaysAvailable
            ),
            'search',
            [
                'method' => Request::METHOD_GET,
                'csrf_protection' => false,
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $limit = $data->getLimit();
            $page = $data->getPage();
            $queryString = $data->getQuery();
            $section = $data->getSection();
            $contentTypes = $data->getContentTypes();
            $lastModified = $data->getLastModified();
            $created = $data->getCreated();
            $creator = $data->getCreator();
            $subtree = $data->getSubtree();
            $searchLanguageCode = ($data->getSearchLanguage() instanceof Language)
                ? $data->getSearchLanguage()->languageCode
                : null;
            $useAlwaysAvailable = $data->getUseAlwaysAvailable();

            $query = new Query();
            $criteria = [];

            if (null !== $queryString) {
                $query->query = new Criterion\FullText($queryString);
            }
            if (null !== $section) {
                $criteria[] = new Criterion\SectionId($section->id);
            }
            if (!empty($contentTypes)) {
                $criteria[] = new Criterion\ContentTypeId(array_column($contentTypes, 'id'));
            }
            if (!empty($lastModified)) {
                $criteria[] = new Criterion\DateMetadata(
                    Criterion\DateMetadata::MODIFIED,
                    Criterion\Operator::BETWEEN,
                    [$lastModified['start_date'], $lastModified['end_date']]
                );
            }
            if (!empty($created)) {
                $criteria[] = new Criterion\DateMetadata(
                    Criterion\DateMetadata::CREATED,
                    Criterion\Operator::BETWEEN,
                    [$created['start_date'], $created['end_date']]
                );
            }
            if ($creator instanceof User) {
                $criteria[] = new Criterion\UserMetadata(
                    Criterion\UserMetadata::OWNER,
                    Criterion\Operator::EQ,
                    $creator->id
                );
            }
            if (null !== $subtree) {
                $criteria[] = new Criterion\Subtree($subtree);
            }
            if (!empty($criteria)) {
                $query->filter = new Criterion\LogicalAnd($criteria);
            }

            if (!$this->searchService->supports(SearchService::CAPABILITY_SCORING)) {
                $query->sortClauses[] = new SortClause\DateModified(Query::SORT_ASC);
            }

            $languageFilter = $this->getSearchLanguageFilter($searchLanguageCode, $useAlwaysAvailable);

            $pagerfanta = new Pagerfanta(
                new ContentSearchAdapter($query, $this->searchService, $languageFilter, $searchLanguageCode)
            );

            $pagerfanta->setMaxPerPage($limit);
            $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

            $editForm = $this->formFactory->contentEdit(
                new ContentEditData()
            );

            return $this->render('@ezdesign/admin/search/search.html.twig', [
                'results' => $this->pagerContentToDataMapper->map($pagerfanta),
                'form' => $form->createView(),
                'pager' => $pagerfanta,
                'form_edit' => $editForm->createView(),
                'filters_expanded' => $data->isFiltered(),
                'user_content_type_identifier' => $this->userContentTypeIdentifier,
            ]);
        }

        return $this->render('@ezdesign/admin/search/search.html.twig', [
            'form' => $form->createView(),
            'filters_expanded' => $form->isSubmitted() && !$form->isValid(),
            'user_content_type_identifier' => $this->userContentTypeIdentifier,
        ]);
    }

    /**
     * @param string|null $languageCode
     * @param bool|null $useAlwaysAvailable
     *
     * @return array
     */
    private function getSearchLanguageFilter(?string $languageCode, ?bool $useAlwaysAvailable): array
    {
        if (empty($languageCode) && null === $useAlwaysAvailable) {
            return [];
        }

        return [
            'languages' => $languageCode ? [$languageCode] : [],
            'useAlwaysAvailable' => $useAlwaysAvailable,
        ];
    }
}
