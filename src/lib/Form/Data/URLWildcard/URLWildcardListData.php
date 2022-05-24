<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Data\URLWildcard;

final class URLWildcardListData
{
    /** @var string|null */
    public $searchQuery;

    /** @var bool|null */
    public $type;

    /** @var int */
    public $limit;

    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }

    public function setSearchQuery(?string $searchQuery): void
    {
        $this->searchQuery = $searchQuery;
    }

    public function getType(): ?bool
    {
        return $this->type;
    }

    public function setType(?bool $type): void
    {
        $this->type = $type;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}
