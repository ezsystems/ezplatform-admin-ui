<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use eZ\Publish\Core\QueryType\QueryType;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Ibexa\AdminUi\Tab\Dashboard\AbstractContentTab;
use Ibexa\AdminUi\Tab\Dashboard\PagerLocationToDataMapper;
use Pagerfanta\Pagerfanta;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class EveryoneContentTab extends AbstractContentTab implements OrderedTabInterface
{
    /** @var \Ibexa\AdminUi\QueryType\ContentLocationSubtreeQueryType */
    private $contentLocationSubtreeQueryType;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PagerLocationToDataMapper $pagerLocationToDataMapper,
        SearchService $searchService,
        QueryType $contentLocationSubtreeQueryType
    ) {
        parent::__construct($twig, $translator, $pagerLocationToDataMapper, $searchService);

        $this->contentLocationSubtreeQueryType = $contentLocationSubtreeQueryType;
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

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     * @throws \eZ\Publish\API\Repository\Exceptions\ForbiddenException
     */
    public function renderView(array $parameters): string
    {
        /** @todo Handle pagination */
        $page = 1;
        $limit = 10;

        $pager = new Pagerfanta(
            new LocationSearchAdapter(
                $this->contentLocationSubtreeQueryType->getQuery(),
                $this->searchService
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->twig->render('@ezdesign/ui/dashboard/tab/all_content.html.twig', [
            'data' => $this->pagerLocationToDataMapper->map($pager, true),
        ]);
    }
}
