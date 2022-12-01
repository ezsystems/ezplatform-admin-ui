<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\Core\QueryType\QueryType;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractMediaTab extends AbstractTab implements OrderedTabInterface
{
    /** @var \Ibexa\AdminUi\Tab\Dashboard\PagerLocationToDataMapper */
    protected $pagerLocationToDataMapper;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \Ibexa\AdminUi\QueryType\MediaLocationSubtreeQueryType */
    protected $mediaLocationSubtreeQueryType;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PagerLocationToDataMapper $pagerLocationToDataMapper,
        SearchService $searchService,
        QueryType $mediaLocationSubtreeQueryType
    ) {
        parent::__construct($twig, $translator);

        $this->pagerLocationToDataMapper = $pagerLocationToDataMapper;
        $this->searchService = $searchService;
        $this->mediaLocationSubtreeQueryType = $mediaLocationSubtreeQueryType;
    }
}
