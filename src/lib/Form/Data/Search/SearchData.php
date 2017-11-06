<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Search;

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

    /**
     * SimpleSearchData constructor.
     *
     * @param int $limit
     * @param int $page
     * @param string|null $query
     */
    public function __construct(int $limit = 10, int $page = 1, ?string $query = null)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->query = $query;
    }

    /**
     * @param int $limit
     *
     * @return SearchData
     */
    public function setLimit(int $limit): SearchData
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $page
     *
     * @return SearchData
     */
    public function setPage(int $page): SearchData
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param string|null $query
     *
     * @return SearchData
     */
    public function setQuery(?string $query): SearchData
    {
        $this->query = $query;

        return $this;
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
}
