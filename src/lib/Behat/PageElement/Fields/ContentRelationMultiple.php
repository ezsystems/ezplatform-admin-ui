<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\ContentRelationTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use PHPUnit\Framework\Assert;

class ContentRelationMultiple extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Content relations (multiple)';

    public const VIEW_PATTERN = '/Multiple relations:[\w\/,: ]* %s [\w \/,:]*/';

    /** @var ContentRelationTable */
    public $contentRelationTable;

    public function __construct(BrowserContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['selectContent'] = '.ez-relations__cta-btn-label';

        $this->contentRelationTable = ElementFactory::createElement($context, ContentRelationTable::ELEMENT_NAME, $this->fields['fieldContainer']);
    }

    public function setValue(array $parameters): void
    {
        $relationsToSet = [];

        foreach (array_keys($parameters) as $parameterKey) {
            $relationsToSet[$parameterKey] = explode('/', $parameters[$parameterKey])[substr_count($parameters[$parameterKey], '/')];
        }
        if (!$this->isRelationEmpty()) {
            $relationsToSet = $this->removeRedundantRelations($relationsToSet);
        }

        if (count($relationsToSet) > 0) {
            $this->startAddingRelations();
            $this->selectRelationsAndConfirm($relationsToSet, $parameters);
        }
    }

    private function removeRedundantRelations(array $wantedRelations): array
    {
        $contentRelationTableHash = $this->contentRelationTable->getTableHash();
        foreach ($contentRelationTableHash as $row) {
            if (!in_array($row['Name'], $wantedRelations)) {
                $this->contentRelationTable->selectListElement($row['Name']);
            } else {
                $key = array_search($row['Name'], $wantedRelations);
                unset($wantedRelations[$key]);
            }
        }

        $this->contentRelationTable->clickTrashIcon();

        return $wantedRelations;
    }

    private function startAddingRelations()
    {
        if ($this->isRelationEmpty()) {
            $this->context->findElement(sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectContent']))->click();
        } else {
            $this->contentRelationTable->clickPlusButton();
        }
    }

    private function selectRelationsAndConfirm($items, $paths)
    {
        $UDW = ElementFactory::createElement($this->context, UniversalDiscoveryWidget::ELEMENT_NAME);
        $itemsToSet = array_keys($items);
        foreach ($itemsToSet as $itemToSet) {
            $UDW->selectContent($paths[$itemToSet]);
        }
        $UDW->confirm();
    }

    public function getValue(): array
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectContent'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        return [$fieldInput->getText()];
    }

    public function verifyValueInItemView(array $values): void
    {
        $explodedValue = explode('/', $values['firstItem']);
        $firstValue = $explodedValue[count($explodedValue) - 1];
        $explodedValue = explode('/', $values['secondItem']);
        $secondValue = $explodedValue[count($explodedValue) - 1];

        Assert::assertRegExp(
            sprintf(self::VIEW_PATTERN, $firstValue),
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
        Assert::assertRegExp(
            sprintf(self::VIEW_PATTERN, $secondValue),
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
    }

    public function isRelationEmpty(): bool
    {
        return $this->context->isElementVisible(sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectContent']));
    }
}
