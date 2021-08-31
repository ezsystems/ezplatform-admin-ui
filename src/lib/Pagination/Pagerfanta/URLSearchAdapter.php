<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\URL\URLQuery;
use Pagerfanta\Adapter\AdapterInterface;

class URLSearchAdapter implements AdapterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Values\URL\URLQuery
     */
    private $query;

    /**
     * @var \eZ\Publish\API\Repository\URLService
     */
    private $urlService;

    /**
     * UrlSearchAdapter constructor.
     *
     * @param \eZ\Publish\API\Repository\Values\URL\URLQuery $query
     * @param \eZ\Publish\API\Repository\URLService $urlService
     */
    public function __construct(URLQuery $query, URLService $urlService)
    {
        $this->query = $query;
        $this->urlService = $urlService;
    }

    /**
     * @inheritdoc
     */
    public function getNbResults(): int
    {
        $query = clone $this->query;
        $query->offset = 0;
        $query->limit = 0;

        return $this->urlService->findUrls($query)->totalCount;
    }

    /**
     * @inheritdoc
     *
     * @return \eZ\Publish\API\Repository\Values\URL\URL[]
     */
    public function getSlice($offset, $length): array
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        return $this->urlService->findUrls($query)->items;
    }
}
