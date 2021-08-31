<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;

class CustomUrlAddData
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    /** @var string|null */
    private $path;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language|null */
    private $language;

    /** @var bool */
    private $redirect;

    /** @var bool */
    private $siteRoot;

    /** @var string|null */
    private $siteAccess;

    public function __construct(
        ?Location $location = null,
        ?string $path = null,
        ?Language $language = null,
        bool $redirect = true,
        bool $siteRoot = true,
        ?string $siteAccess = null
    ) {
        $this->location = $location;
        $this->path = $path;
        $this->language = $language;
        $this->redirect = $redirect;
        $this->siteRoot = $siteRoot;
        $this->siteAccess = $siteAccess;
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
     * @return CustomUrlAddData
     */
    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     *
     * @return CustomUrlAddData
     */
    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     *
     * @return CustomUrlAddData
     */
    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRedirect(): bool
    {
        return $this->redirect;
    }

    /**
     * @param bool $redirect
     *
     * @return CustomUrlAddData
     */
    public function setRedirect(bool $redirect): self
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSiteRoot(): bool
    {
        return $this->siteRoot;
    }

    /**
     * @param bool $siteRoot
     *
     * @return CustomUrlAddData
     */
    public function setSiteRoot(bool $siteRoot): self
    {
        $this->siteRoot = $siteRoot;

        return $this;
    }

    public function getSiteAccess(): ?string
    {
        return $this->siteAccess;
    }

    public function setSiteAccess(?string $siteAccess): self
    {
        $this->siteAccess = $siteAccess;

        return $this;
    }
}
