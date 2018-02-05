<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Exception\ElementNotFoundException;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class AdminList extends Element
{
    protected $fields = ['list' => 'form',
                        'plusButton' => '.ez-icon-create',
                        'trashButton' => '.ez-icon-trash',
                        'editButton' => 'tr:nth-child(%s) a[title=Edit]',
                        'listHeader' => '.ez-table-header__headline',
                        'tableHeader' => 'th',
                        'listElementLink' => '.ez-checkbox-cell+ td a',
                        'tableCell' => 'tr:nth-child(%s) td:nth-child(%s)', ];

    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Admin List';
    protected $listHeader;

    public function __construct(UtilityContext $context, string $listHeader)
    {
        parent::__construct($context);
        $this->listHeader = $listHeader;
    }

    private function verifyProperList(): void
    {
        if ($this->listHeader !== $this->context->findElement($this->fields['listHeader'], $this->defaultTimeout)->getText()) {
            throw new ElementNotFoundException($this->context->getSession(), 'table header', $this->fields['listHeader']);
        }
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['plusButton'], $this->defaultTimeout);
        $this->context->waitUntilElementIsVisible($this->fields['trashButton'], $this->defaultTimeout);
        $this->context->waitUntilElementIsVisible($this->fields['listHeader'], $this->defaultTimeout);

        $this->verifyProperList();
    }

    public function clickPlusButton(): void
    {
        $this->context->findElement($this->fields['plusButton'])->click();
    }

    public function clickTrashButton(): void
    {
        $this->context->findElement($this->fields['trashButton'])->click();
    }

    public function isListElementSelectable(string $name): bool
    {
        $position = $this->context->getElementPositionByText($name, $this->fields['listElementLink']);
        $checkbox = $this->context->findElement(sprintf($this->fields['tableCell'], $position, 1) . ' .form-check-input')->getAttribute('disabled');

        return $checkbox !== 'disabled';
    }

    public function selectListElement(string $name): void
    {
        $position = $this->context->getElementPositionByText($name, $this->fields['listElementLink']);
        $this->context->findElement(sprintf($this->fields['tableCell'], $position, 1))->checkField('');
    }

    public function clickListElement(string $name): void
    {
        $this->context->getElementByText($name, $this->fields['listElementLink'])->click();
    }

    public function isElementOnList(string $name): bool
    {
        return $this->context->getElementByText($name, $this->fields['listElementLink']) !== null;
    }

    public function getListItemAttribute(string $name, string $header): string
    {
        $columnPosition = $this->context->getElementPositionByText($header, $this->fields['tableHeader'], null, $this->context->findElement($this->fields['list']));
        $rowPosition = $this->context->getElementPositionByText($name, $this->fields['listElementLink'], null, $this->context->findElement($this->fields['list']));

        return $this->context->findElement(sprintf($this->fields['tableCell'], $rowPosition, $columnPosition))->getText();
    }

    public function clickEditButton(string $listItemName): void
    {
        $position = $this->context->getElementPositionByText($listItemName, $this->fields['listElementLink']);
        $this->context->findElement(sprintf($this->fields['editButton'], $position))->click();
    }
}
