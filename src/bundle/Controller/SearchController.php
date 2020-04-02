<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchHitAdapter;
use eZ\Publish\Core\QueryType\QueryType;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Search\PagerSearchContentToDataMapper;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \EzSystems\EzPlatformAdminUi\Search\PagerSearchContentToDataMapper */
    private $pagerSearchContentToDataMapper;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \eZ\Publish\API\Repository\SectionService */
    private $sectionService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\QueryType\SearchQueryType */
    private $searchQueryType;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        SearchService $searchService,
        PagerSearchContentToDataMapper $pagerSearchContentToDataMapper,
        FormFactory $formFactory,
        SectionService $sectionService,
        ContentTypeService $contentTypeService,
        QueryType $searchQueryType,
        ConfigResolverInterface $configResolver
    ) {
        $this->searchService = $searchService;
        $this->pagerSearchContentToDataMapper = $pagerSearchContentToDataMapper;
        $this->formFactory = $formFactory;
        $this->sectionService = $sectionService;
        $this->contentTypeService = $contentTypeService;
        $this->searchQueryType = $searchQueryType;
        $this->configResolver = $configResolver;
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
        $limit = $search['limit'] ?? $this->configResolver->getParameter('pagination.search_limit');
        $page = $search['page'] ?? 1;
        $query = $search['query'];
        $section = null;
        $creator = null;
        $contentTypes = [];
        $lastModified = $search['last_modified'] ?? [];
        $created = $search['created'] ?? [];
        $subtree = $search['subtree'] ?? null;
        $searchLanguage = null;

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
                $searchLanguage
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
            $queryString = $data->getQuery();
            $searchLanguageCode = ($data->getSearchLanguage() instanceof Language)
                ? $data->getSearchLanguage()->languageCode
                : null;
            $languageFilter = $this->getSearchLanguageFilter($searchLanguageCode, $queryString);

            $pagerfanta = new Pagerfanta(
                new ContentSearchHitAdapter(
                    $this->searchQueryType->getQuery(['search_data' => $data]),
                    $this->searchService,
                    $languageFilter
                )
            );
            $pagerfanta->setMaxPerPage($data->getLimit());
            $pagerfanta->setCurrentPage(min($data->getPage(), $pagerfanta->getNbPages()));

            $editForm = $this->formFactory->contentEdit(
                new ContentEditData()
            );

            return $this->render('@ezdesign/ui/search/index.html.twig', [
                'results' => $this->pagerSearchContentToDataMapper->map($pagerfanta),
                'form' => $form->createView(),
                'pager' => $pagerfanta,
                'form_edit' => $editForm->createView(),
                'user_content_type_identifier' => $this->configResolver->getParameter('user_content_type_identifier'),
            ]);
        }

        return $this->render('@ezdesign/ui/search/index.html.twig', [
            'form' => $form->createView(),
            'user_content_type_identifier' => $this->configResolver->getParameter('user_content_type_identifier'),
        ]);
    }

    private function getSearchLanguageFilter(?string $languageCode, ?string $queryString): array
    {
        $filter = [
            'languages' => !empty($languageCode) ? [$languageCode] : [],
            'useAlwaysAvailable' => true,
        ];

        if (!empty($queryString)) {
            $filter['excludeTranslationsFromAlwaysAvailable'] = false;
        }

        return $filter;
    }
}
