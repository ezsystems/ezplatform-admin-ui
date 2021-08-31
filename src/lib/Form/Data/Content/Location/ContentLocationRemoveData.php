<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Location;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\Validator\Constraints as Assert;

class ContentLocationRemoveData
{
    /**
     * @Assert\NotBlank()
     *
     * @var \eZ\Publish\API\Repository\Values\Content\ContentInfo|null
     */
    public $contentInfo;

    /**
     * @todo add more validation constraints
     *
     * @Assert\NotBlank()
     *
     * @var array
     */
    public $locations;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param array $selectedLocations
     */
    public function __construct(
        ?ContentInfo $contentInfo = null,
        array $selectedLocations = []
    ) {
        $this->contentInfo = $contentInfo;
        $this->locations = $selectedLocations;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     */
    public function setContentInfo(?ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return array
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @param array $locations
     */
    public function setLocations(array $locations)
    {
        $this->locations = $locations;
    }
}
