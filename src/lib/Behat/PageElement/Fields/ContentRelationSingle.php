<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\ContentRelationTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use PHPUnit\Framework\Assert;

class ContentRelationSingle extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Content relation (single)';

    public const VIEW_PATTERN = '/Single relation:[\w\/,: ]* %s [\w \/,:]*/';

    /** @var ContentRelationTable */
    public $contentRelationTable;

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['selectContent'] = '.ez-relations__cta-btn-label';

        $this->contentRelationTable = ElementFactory::createElement($context, ContentRelationTable::ELEMENT_NAME, $this->fields['fieldContainer']);
    }

    public function setValue(array $parameters): void
    {
        if (!$this->isRelationEmpty()) {
            $itemName = explode('/', $parameters['value'])[substr_count($parameters['value'], '/')];
            if (!$this->contentRelationTable->isElementOnCurrentPage($itemName)) {
                $this->removeActualRelation();
            } else {
                return;
            }
        }

        $selectContent = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectContent'])
        );

        Assert::assertNotNull($selectContent, sprintf('Select content button for field %s not found.', $this->label));

        $selectContent->click();

        $UDW = ElementFactory::createElement($this->context, UniversalDiscoveryWidget::ELEMENT_NAME);
        $UDW->selectContent($parameters['value']);
        $UDW->confirm();
    }

    public function removeActualRelation(): void
    {
        $actualItemName = $this->contentRelationTable->getCellValue(1, 2);
        $this->contentRelationTable->selectListElement($actualItemName);
        $this->contentRelationTable->clickTrashIcon();
    }

    public function getValue(): array
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectContent'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        return [$this->contentRelationTable->getCellValue(1, 2)];
    }

    public function verifyValueInItemView(array $values): void
    {
        $explodedValue = explode('/', $values['value']);
        $value = $explodedValue[count($explodedValue) - 1];

        Assert::assertRegExp(
            sprintf(self::VIEW_PATTERN, $value),
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
    }

    public function isRelationEmpty(): bool
    {
        return $this->context->isElementVisible(sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectContent']));
    }
}
