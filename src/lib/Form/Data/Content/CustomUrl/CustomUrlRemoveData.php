<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl;

use eZ\Publish\API\Repository\Values\Content\Location;

class CustomUrlRemoveData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    /** @var array */
    private $urlAliases;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param array $urlAliases
     */
    public function __construct(
        ?Location $location = null,
        array $urlAliases = []
    ) {
        $this->location = $location;
        $this->urlAliases = $urlAliases;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlRemoveData
     */
    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return array
     */
    public function getUrlAliases(): array
    {
        return $this->urlAliases;
    }

    /**
     * @param array $urlAliases
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlRemoveData
     */
    public function setUrlAliases(array $urlAliases): self
    {
        $this->urlAliases = $urlAliases;

        return $this;
    }
}
