<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\BrowseTree;

class Node
{
    /** @var ?int */
    public $id = null;

    /** @var string */
    public $text = '';

    /** @var array */
    public $li_attr = [];

    /** @var array */
    public $a_attr = [];

    /** @var array */
    public $state = [];

    /** @var array */
    public $children = [];

    /** @var string */
    public $type = '';

    /** @var ?string */
    public $icon = null;

    /**
     * Node constructor.
     *
     * @param int         $id
     * @param string      $text
     * @param array       $liAttr
     * @param array       $aAttr
     * @param array       $state
     * @param array       $children
     * @param string      $type
     * @param null|string $icon
     */
    public function __construct(
        ?int $id = null,
        string $text = '',
        array $liAttr = [],
        array $aAttr = [],
        array $state = [],
        array $children = [],
        string $type = '',
        ?string $icon = null
    ) {
        $this->id = $id;
        $this->text = $text;
        $this->li_attr = $liAttr;
        $this->a_attr = $aAttr;
        $this->state = $state;
        $this->children = $children;
        $this->type = $type;
        $this->icon = $icon;
    }
}