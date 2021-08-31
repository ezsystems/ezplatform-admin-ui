<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value\ContentTree;

use EzSystems\EzPlatformRest\Value as RestValue;

class Node extends RestValue
{
    /** @var int */
    private $depth;

    /** @var int */
    public $locationId;

    /** @var int */
    public $contentId;

    /** @var string */
    public $name;

    /** @var string */
    public $contentTypeIdentifier;

    /** @var bool */
    public $isContainer;

    /** @var bool */
    public $isInvisible;

    /** @var int */
    public $displayLimit;

    /** @var int */
    public $totalChildrenCount;

    /** @var \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Node[] */
    public $children;

    /**
     * @param int $depth
     * @param int $locationId
     * @param int $contentId
     * @param string $name
     * @param string $contentTypeIdentifier
     * @param bool $isContainer
     * @param bool $isInvisible
     * @param int $displayLimit
     * @param int $totalChildrenCount
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Node[] $children
     */
    public function __construct(
        int $depth,
        int $locationId,
        int $contentId,
        string $name,
        string $contentTypeIdentifier,
        bool $isContainer,
        bool $isInvisible,
        int $displayLimit,
        int $totalChildrenCount,
        array $children = []
    ) {
        $this->depth = $depth;
        $this->locationId = $locationId;
        $this->contentId = $contentId;
        $this->name = $name;
        $this->isInvisible = $isInvisible;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->isContainer = $isContainer;
        $this->totalChildrenCount = $totalChildrenCount;
        $this->displayLimit = $displayLimit;
        $this->children = $children;
    }
}
