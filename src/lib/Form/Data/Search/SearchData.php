<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Search;

use eZ\Publish\API\Repository\Values\Content\Section;
use Symfony\Component\Validator\Constraints as Assert;

class SearchData
{
    /**
     * @var int
     *
     * @Assert\Range(
     *     max = 1000
     * )
     */
    private $limit;

    /** @var int */
    private $page;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $query;

    /** @var \eZ\Publish\API\Repository\Values\Content\Section */
    private $section;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType[] */
    private $contentTypes;

    /** @var array */
    private $lastModified;

    /** @var array */
    private $created;

    /**
     * SimpleSearchData constructor.
     *
     * @param int $limit
     * @param int $page
     * @param string|null $query
     * @param \eZ\Publish\API\Repository\Values\Content\Section|null $section
     * @param array $contentTypes
     * @param array $lastModified
     * @param array $created
     */
    public function __construct(
        int $limit = 10,
        int $page = 1,
        ?string $query = null,
        ?Section $section = null,
        array $contentTypes = [],
        array $lastModified = [],
        array $created = []
    ) {
        $this->limit = $limit;
        $this->page = $page;
        $this->query = $query;
        $this->section = $section;
        $this->contentTypes = $contentTypes;
        $this->lastModified = $lastModified;
        $this->created = $created;
    }

    /**
     * @param int $limit
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $page
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param string|null $query
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData
     */
    public function setQuery(?string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section|null $section
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData
     */
    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType[] $contentTypes
     */
    public function setContentTypes(array $contentTypes): void
    {
        $this->contentTypes = $contentTypes;
    }

    /**
     * @param array $lastModified
     */
    public function setLastModified(array $lastModified): void
    {
        $this->lastModified = $lastModified;
    }

    /**
     * @param array $created
     */
    public function setCreated(array $created): void
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Section|null
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType[]
     */
    public function getContentTypes(): array
    {
        return $this->contentTypes;
    }

    /**
     * @return array
     */
    public function getLastModified(): array
    {
        return $this->lastModified;
    }

    /**
     * @return array
     */
    public function getCreated(): array
    {
        return $this->created;
    }
}
