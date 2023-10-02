<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;
use Psr\Log\LoggerInterface;

class CustomUrlsDataset
{
    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\UrlAlias[] */
    private $data;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(
        URLAliasService $urlAliasService,
        ValueFactory $valueFactory,
        LoggerInterface $logger
    ) {
        $this->urlAliasService = $urlAliasService;
        $this->valueFactory = $valueFactory;
        $this->logger = $logger;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\CustomUrlsDataset
     */
    public function load(Location $location): self
    {
        try {
            $customUrlAliases = $this->urlAliasService->listLocationAliases(
                $location,
                true,
                null,
                true
            );
        } catch (BadStateException $e) {
            $this->logger->warning(
                sprintf(
                    'At least one custom alias belonging to location %d is broken. Fix it by using the ezplatform:urls:regenerate-aliases command.',
                    $location->id
                ),
                ['exception' => $e]
            );
            $customUrlAliases = [];
        }

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
