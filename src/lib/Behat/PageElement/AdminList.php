<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Browser\Element\Element;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\Table;
use PHPUnit\Framework\Assert;

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

    public function __construct(BrowserContext $context, string $listHeader, string $tableName, ?string $containerLocator = 'section')
    {
        parent::__construct($context);
        $containerLocator = !$containerLocator ? 'section' : $containerLocator;
        $this->listHeader = $listHeader;
        $this->fields = [
            'list' => $containerLocator,
            'listHeader' => '.ez-table-header .ez-table-header__headline, header .ez-table__headline, header h5',
            'plusButton' => '.ez-icon-create',
            'trashButton' => '.ez-icon-trash,button[data-original-title^="Delete"]',
            'mainAssignButton' => '.ez-table-header [data-original-title^=Assign]',
            'paginationNextButton' => '.ez-pagination a.page-link[rel="next"]',
        ];
        $this->listContainer = $this->context->findElement($containerLocator);
        $this->table = ElementFactory::createElement($context, $tableName, $containerLocator);
    }

    public function verifyVisibility(): void
    {
        $actualHeader = $this->context->getElementByTextFragment($this->listHeader, $this->fields['list'] . ' ' . $this->fields['listHeader']);
        if ($actualHeader === null) {
            Assert::fail(sprintf('Table header "%s" not found on page', $this->listHeader));
        }

        $this->table->verifyVisibility();
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

    public function isElementOnTheList(string $listElementName): bool
    {
        return $this->table->isElementInTable($listElementName);
    }
}
