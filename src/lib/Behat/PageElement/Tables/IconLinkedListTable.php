<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use EzSystems\Behat\Browser\Context\BrowserContext;

class IconLinkedListTable extends LinkedListTable
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Icon Linked List Table';

    public function __construct(BrowserContext $context, $containerLocator)
    {
        parent::__construct($context, $containerLocator);

        $this->fields['listElement'] = $this->fields['list'] . ' .ez-table__cell--after-icon a';
    }
}
