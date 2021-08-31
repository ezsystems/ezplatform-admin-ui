<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Search;

use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;

class TrashSearchData
{
    /** @var int|null */
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

    public function __construct(
        ?int $limit = 10,
        ?int $page = 1,
        ?Section $section = null,
        ?ContentType $contentType = null,
        ?string $trashed = null,
        ?array $trashedInterval = [],
        ?User $creator = null,
        ?array $sort = ['field' => 'trashed', 'direction' => 0]
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

    public function getTrashed(): ?string
    {
        return $this->trashed;
    }

    public function setTrashed(?string $trashed): void
    {
        $this->trashed = $trashed;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): void
    {
        $this->section = $section;
    }

    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function getTrashedInterval(): ?array
    {
        return $this->trashedInterval;
    }

    public function setTrashedInterval(?array $trashedInterval): void
    {
        $this->trashedInterval = $trashedInterval;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): void
    {
        $this->creator = $creator;
    }

    public function getSort(): ?array
    {
        return $this->sort;
    }

    public function setSort(?array $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return bool
     */
    public function isFiltered(): bool
    {
        $contentType = $this->getContentType();
        $section = $this->getSection();
        $trashed = $this->getTrashedInterval();
        $creator = $this->getCreator();

        return
            null !== $contentType ||
            null !== $section ||
            null !== $creator ||
            !empty($trashed);
    }
}
