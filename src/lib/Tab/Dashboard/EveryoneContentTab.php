<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class EveryoneContentTab extends AbstractTab implements OrderedTabInterface
{
    /** @var PagerContentToDataMapper */
    protected $pagerContentToDataMapper;

    /** @var SearchService */
    protected $searchService;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param PagerContentToDataMapper $pagerContentToDataMapper
     * @param SearchService $searchService
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PagerContentToDataMapper $pagerContentToDataMapper,
        SearchService $searchService
    ) {
        parent::__construct($twig, $translator);

        $this->pagerContentToDataMapper = $pagerContentToDataMapper;
        $this->searchService = $searchService;
    }

    public function getIdentifier(): string
    {
        return 'everyone-content';
    }

    public function getName(): string
    {
        return /** @Desc("Content") */
            $this->translator->trans('tab.name.everyone_content', [], 'dashboard');
    }

    public function getOrder(): int
    {
        return 100;
    }

    public function renderView(array $parameters): string
    {
        /** @todo Handle pagination */
        $page = 1;
        $limit = 10;

        /** @todo subtree shouldn't be hardcoded! */
        $pager = new Pagerfanta(
            new ContentSearchAdapter(
                new SubtreeQuery('/1/2/'),
                $this->searchService
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->twig->render('@ezdesign/ui/dashboard/tab/all_content.html.twig', [
            'data' => $this->pagerContentToDataMapper->map($pager),
        ]);
    }
}
