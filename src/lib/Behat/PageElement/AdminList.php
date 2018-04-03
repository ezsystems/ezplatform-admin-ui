<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Exception\ElementNotFoundException;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

/** Element that describes list-table structure that repeats in every Admin pages */
class AdminList extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Admin List';
    /** @var string list table title placed in the blue bar */
    protected $listHeader;
    /** @var \Behat\Mink\Element\NodeElement|null element containing admin list */
    protected $listContainer;
    /** @var Table */
    public $table;

    public function __construct(UtilityContext $context, string $listHeader, string $tableName, ?string $containerLocator = 'section')
    {
        parent::__construct($context);
        $this->listHeader = $listHeader;
        $this->fields = [
            'list' => $containerLocator,
            'listHeader' => '.ez-table-header__headline, header h5',
            'plusButton' => '.ez-icon-create',
            'trashButton' => '.ez-icon-trash,button[title^="Delete"]',
            'mainAssignButton' => '.ez-table-header [title^=Assign]',
        ];
        $this->listContainer = $this->context->findElement($containerLocator);
        $this->table = ElementFactory::createElement($context, $tableName, $containerLocator);
    }

    public function verifyVisibility(): void
    {
        $actualHeader = $this->context->getElementByTextFragment($this->listHeader, $this->fields['listHeader'], null, $this->listContainer);
        if ($actualHeader === null) {
            throw new ElementNotFoundException($this->context->getSession(), sprintf('table header "%s"', $this->listHeader), $this->fields['listHeader']);
        }
    }

    public function clickPlusButton(): void
    {
        $this->context->findElement($this->fields['plusButton'], $this->defaultTimeout, $this->listContainer)->click();
    }

    public function clickTrashButton(): void
    {
        $this->context->findElement($this->fields['trashButton'], $this->defaultTimeout, $this->listContainer)->click();
    }

    public function clickAssignButton(?string $listItemName = null): void
    {
        if ($listItemName === null) {
            $this->context->findElement($this->fields['mainAssignButton'], $this->defaultTimeout, $this->listContainer)->click();
        } else {
            $this->table->clickAssignButton($listItemName);
        }
    }
}
