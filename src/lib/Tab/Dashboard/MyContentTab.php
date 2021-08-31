<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use eZ\Publish\Core\QueryType\QueryType;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MyContentTab extends AbstractTab implements OrderedTabInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Tab\Dashboard\PagerContentToDataMapper */
    protected $pagerContentToDataMapper;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \eZ\Publish\Core\QueryType\QueryType */
    private $contentSubtreeQueryType;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\Tab\Dashboard\PagerContentToDataMapper $pagerContentToDataMapper
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \eZ\Publish\Core\QueryType\QueryType $contentSubtreeQueryType
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PagerContentToDataMapper $pagerContentToDataMapper,
        SearchService $searchService,
        QueryType $contentSubtreeQueryType
    ) {
        parent::__construct($twig, $translator);

        $this->pagerContentToDataMapper = $pagerContentToDataMapper;
        $this->searchService = $searchService;
        $this->contentSubtreeQueryType = $contentSubtreeQueryType;
    }

    public function getIdentifier(): string
    {
        return 'my-content';
    }

    public function getName(): string
    {
        return /** @Desc("Content") */
            $this->translator->trans('tab.name.my_content', [], 'dashboard');
    }

    public function getOrder(): int
    {
        return 200;
    }

    public function renderView(array $parameters): string
    {
        /** @todo Handle pagination */
        $page = 1;
        $limit = 10;

        $pager = new Pagerfanta(
            new ContentSearchAdapter(
                $this->contentSubtreeQueryType->getQuery(['owned' => true]),
                $this->searchService
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->twig->render('@ezdesign/ui/dashboard/tab/my_content.html.twig', [
            'data' => $this->pagerContentToDataMapper->map($pager),
        ]);
    }
}
