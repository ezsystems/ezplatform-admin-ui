<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Search;

use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;
use Symfony\Component\Validator\Constraints as Assert;

class TrashSearchData
{
    /**
     * @var int|null
     *
     * @Assert\Range(
     *     max = 500
     * )
     */
    private $limit;

    /** @var int|null */
    private $page;

    /** @var \eZ\Publish\API\Repository\Values\Content\Section|null */
    private $section;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|null */
    private $contentType;

    /** @var array|null */
    private $trashedInterval;

    /** @var string|null */
    private $trashed;

    /** @var \eZ\Publish\API\Repository\Values\User\User|null */
    private $creator;

    /** @var array|null */
    private $sort;

    /**
     * @param int|null $limit
     * @param int|null $page
     * @param \eZ\Publish\API\Repository\Values\Content\Section|null $section
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     * @param string|null $trashed
     * @param array|null $trashedInterval
     * @param \eZ\Publish\API\Repository\Values\User\User|null $creator
     */
    public function __construct(
        ?int $limit = 10,
        ?int $page = 1,
        ?Section $section = null,
        ?ContentType $contentType = null,
        ?string $trashed = null,
        ?array $trashedInterval = [],
        ?User $creator = null,
        ?array $sort = []
    ) {
        $this->limit = $limit;
        $this->page = $page;
        $this->section = $section;
        $this->contentType = $contentType;
        $this->trashed = $trashed;
        $this->trashedInterval = $trashedInterval;
        $this->creator = $creator;
        $this->sort = $sort;
    }

    /**
     * @return string|null
     */
    public function getTrashed(): ?string
    {
        return $this->trashed;
    }

    /**
     * @param string|null $trashed
     */
    public function setTrashed(?string $trashed): void
    {
        $this->trashed = $trashed;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @param int|null $page
     */
    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Section|null
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section|null $section
     */
    public function setSection(?Section $section): void
    {
        $this->section = $section;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType|null
     */
    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     */
    public function setContentType(?ContentType $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @return array|null
     */
    public function getTrashedInterval(): ?array
    {
        return $this->trashedInterval;
    }

    /**
     * @param array|null $trashedInterval
     */
    public function setTrashedInterval(?array $trashedInterval): void
    {
        $this->trashedInterval = $trashedInterval;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\User|null
     */
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User|null $creator
     */
    public function setCreator(?User $creator): void
    {
        $this->creator = $creator;
    }

    /**
     * @return array|null
     */
    public function getSort(): ?array
    {
        return $this->sort;
    }

    /**
     * @param array|null $sort
     */
    public function setSort(?array $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return bool
     */
    public function isFiltered(): bool
    {
        $contentTypes = $this->getContentType();
        $section = $this->getSection();
        $trashed = $this->getTrashedInterval();
        $creator = $this->getCreator();

        return
            !empty($contentTypes) ||
            null !== $section ||
            !empty($trashed) ||
            !empty($creator);
    }
}
