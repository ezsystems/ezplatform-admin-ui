<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft;

class ContentRemoveData
{
    /** @var array|null */
    private $versions;

    /**
     * @param array|null $versions
     */
    public function __construct(?array $versions = null)
    {
        $this->versions = $versions;
    }

    /**
     * @return array|null
     */
    public function getVersions(): ?array
    {
        return $this->versions;
    }

    /**
     * @param array|null $versions
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentRemoveData
     */
    public function setVersions(?array $versions): self
    {
        $this->versions = $versions;

        return $this;
    }
}
