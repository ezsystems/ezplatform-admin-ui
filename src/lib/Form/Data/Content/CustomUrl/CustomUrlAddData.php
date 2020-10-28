<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;

class CustomUrlAddData
{
    /** @var Location|null */
    private $location;

    /** @var string|null */
    private $path;

    /** @var Language|null */
    private $language;

    /** @var bool */
    private $redirect;

    /** @var bool */
    private $siteRoot;

    /** @var int|null */
    private $rootLocationId;

    public function __construct(
        ?Location $location = null,
        ?string $path = null,
        ?Language $language = null,
        bool $redirect = true,
        bool $siteRoot = true,
        ?int $rootLocationId = null
    ) {
        $this->location = $location;
        $this->path = $path;
        $this->language = $language;
        $this->redirect = $redirect;
        $this->siteRoot = $siteRoot;
        $this->rootLocationId = $rootLocationId;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location|null $location
     *
     * @return CustomUrlAddData
     */
    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language|null $language
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

    public function getRootLocationId(): ?int
    {
        return $this->rootLocationId;
    }

    public function setRootLocationId(?int $rootLocationId): self
    {
        $this->rootLocationId = $rootLocationId;

        return $this;
    }
}
