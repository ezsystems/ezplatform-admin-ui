<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\URL\URL;
use Pagerfanta\Adapter\AdapterInterface;

class URLUsagesAdapter implements AdapterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\URLService
     */
    private $urlService;

    /**
     * @var \eZ\Publish\API\Repository\Values\URL\URL
     */
    private $url;

    /**
     * @param \eZ\Publish\API\Repository\Values\URL\URL $url
     * @param \eZ\Publish\API\Repository\URLService $urlService
     */
    public function __construct(URL $url, URLService $urlService)
    {
        $this->urlService = $urlService;
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function getNbResults(): int
    {
        return $this->urlService->findUsages($this->url, 0, 0)->totalCount;
    }

    /**
     * @inheritdoc
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo[]
     */
    public function getSlice($offset, $length): array
    {
        return $this->urlService->findUsages($this->url, $offset, $length)->items;
    }
}
