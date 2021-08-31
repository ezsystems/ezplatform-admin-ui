<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class CustomUrlsDataset
{
    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\UrlAlias[] */
    private $data;

    /**
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(
        URLAliasService $urlAliasService,
        ValueFactory $valueFactory
    ) {
        $this->urlAliasService = $urlAliasService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\CustomUrlsDataset
     */
    public function load(Location $location): self
    {
        $customUrlAliases = $this->urlAliasService->listLocationAliases($location, true, null, true);
        $this->data = array_map(
            function (URLAlias $urlAlias) {
                return $this->valueFactory->createUrlAlias($urlAlias);
            },
            $customUrlAliases
        );

        return $this;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\UrlAlias[]
     */
    public function getCustomUrlAliases(): array
    {
        return $this->data;
    }
}
