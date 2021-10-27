<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class ContentRelationMultiple extends FieldTypeComponent
{
    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableBuilder */
    private $tableBuilder;

    public function __construct(Session $session, UniversalDiscoveryWidget $universalDiscoveryWidget, TableBuilder $tableBuilder)
    {
        parent::__construct($session);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->tableBuilder = $tableBuilder;
    }

    public function setParentLocator(VisibleCSSLocator $locator): void
    {
        parent::setParentLocator($locator);
        $this->table = $this->tableBuilder
            ->newTable()
            ->withParentLocator($this->parentLocator)
            ->withRowLocator($this->getLocator('relationRow'))
            ->withColumnLocator($this->getLocator('columnHeader'))
            ->build();
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

        if (!empty($relationsToSet)) {
            $this->startAddingRelations();
            $this->selectRelationsAndConfirm($relationsToSet, $parameters);
        }
    }

    public function getValue(): array
    {
        $selectLocator = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('selectContent'))
            ->build()
        ;

        return [
            $this->getHTMLPage()->find($selectLocator)->getText(),
        ];
    }

    public function verifyValueInItemView(array $values): void
    {
        $explodedValue = explode('/', $values['firstItem']);
        $firstValue = $explodedValue[count($explodedValue) - 1];
        $explodedValue = explode('/', $values['secondItem']);
        $secondValue = $explodedValue[count($explodedValue) - 1];

        $viewPatternRegex = '/Multiple relations[\w\/,: ]* %s [\w \/,:]*/';
        Assert::assertRegExp(
            sprintf($viewPatternRegex, $firstValue),
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Field has wrong value'
        );
        Assert::assertRegExp(
            sprintf($viewPatternRegex, $secondValue),
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Field has wrong value'
        );
    }

    public function isRelationEmpty(): bool
    {
        $selectLocator = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('selectContent'))
            ->build()
        ;

        return $this->getHTMLPage()->findAll($selectLocator)->any();
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('selectContent', '.ibexa-relations__cta-btn-label'),
            new VisibleCSSLocator('buttonRemove', '.ibexa-relations__table-action--remove'),
            new VisibleCSSLocator('columnHeader', 'tr:not(.ibexa-relations__table-header) th'),
            new VisibleCSSLocator('removeRelations', '.ibexa-relations__table-action--remove'),
            new VisibleCSSLocator('addRelation', '.ibexa-relations__table-action--create'),
            new VisibleCSSLocator('relationRow', '.ibexa-relations__list tr'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezobjectrelationlist';
    }

    private function removeRedundantRelations(array $wantedRelations): array
    {
        $currentContentRelations = array_column($this->table->getColumnValues(['Name']), 'Name');
        foreach ($currentContentRelations as $relationPosition => $currentRelation) {
            if (!in_array($currentRelation, array_values($wantedRelations), true)) {
                $this->table->getTableRowByIndex($relationPosition)->select();
            } else {
                $key = array_search($currentRelation, $wantedRelations, true);
                unset($wantedRelations[$key]);
            }
        }

        $removeRelationsLocator = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('removeRelations'))
            ->build();

        $this->getHTMLPage()
            ->find($removeRelationsLocator)
            ->click();

        return $wantedRelations;
    }

    private function startAddingRelations()
    {
        if ($this->isRelationEmpty()) {
            $selectLocator = CSSLocatorBuilder::base($this->parentLocator)
                ->withDescendant($this->getLocator('selectContent'))
                ->build();
            $this->getHTMLPage()->find($selectLocator)->click();
        } else {
            $addRelationLocator = CSSLocatorBuilder::base($this->parentLocator)
                ->withDescendant($this->getLocator('addRelation'))
                ->build();
            $this->getHTMLPage()
                ->find($addRelationLocator)
                ->click();
        }
    }

    private function selectRelationsAndConfirm($items, $paths)
    {
        $this->universalDiscoveryWidget->verifyIsLoaded();

        $itemsToSet = array_keys($items);
        foreach ($itemsToSet as $itemToSet) {
            $this->universalDiscoveryWidget->selectContent($paths[$itemToSet]);
        }
        $this->universalDiscoveryWidget->confirm();
    }
}
