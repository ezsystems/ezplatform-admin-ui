<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Ibexa\AdminUi\Tab\Dashboard\AbstractMediaTab;
use Pagerfanta\Pagerfanta;

class EveryoneMediaTab extends AbstractMediaTab implements OrderedTabInterface
{
    public function getIdentifier(): string
    {
        return 'everyone-media';
    }

    public function getName(): string
    {
        return /** @Desc("Media") */
            $this->translator->trans('tab.name.everyone_media', [], 'dashboard');
    }

    public function getOrder(): int
    {
        return 200;
    }

    /**
     * @inheritdoc
     */
    public function renderView(array $parameters): string
    {
        /** @todo Handle pagination */
        $page = 1;
        $limit = 10;

        $pager = new Pagerfanta(
            new LocationSearchAdapter(
                $this->mediaLocationSubtreeQueryType->getQuery(),
                $this->searchService
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->twig->render('@ezdesign/ui/dashboard/tab/all_media.html.twig', [
            'data' => $this->pagerLocationToDataMapper->map($pager, true),
        ]);
    }
}
