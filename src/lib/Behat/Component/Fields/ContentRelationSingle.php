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

class ContentRelationSingle extends FieldTypeComponent
{
    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableBuilder */
    private $tableBuilder;

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('selectContent', '.ibexa-relations__cta-btn-label'),
            new VisibleCSSLocator('buttonRemove', '.ibexa-relations__table-action--remove-item'),
            new VisibleCSSLocator('relationRow', '.ibexa-relations__list tr'),
            new VisibleCSSLocator('columnHeader', 'tr:not(.ibexa-relations__table-header) th'),
        ];
    }

    public function __construct(Session $session, UniversalDiscoveryWidget $universalDiscoveryWidget, TableBuilder $tableBuilder)
    {
        parent::__construct($session);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->tableBuilder = $tableBuilder;
    }

    public function setValue(array $parameters): void
    {
        if (!$this->isRelationEmpty()) {
            $this->getHTMLPage()->find($this->getLocator('buttonRemove'))->click();
        }

        $this->getHTMLPage()
            ->find(
                CSSLocatorBuilder::base($this->parentLocator)
                    ->withDescendant($this->getLocator('selectContent'))
                    ->build()
            )
            ->click();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($parameters['value']);
        $this->universalDiscoveryWidget->confirm();
    }

    public function getValue(): array
    {
        $names = $this->table->getColumnValues(['Name']);

        return [$names[0]['Name']];
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

    public function verifyValueInItemView(array $values): void
    {
        $explodedValue = explode('/', $values['value']);
        $value = $explodedValue[count($explodedValue) - 1];

        $viewPatternRegex = '/Single relation[\w\/,: ]* %s [\w \/,:]*/';

        Assert::assertRegExp(
            sprintf($viewPatternRegex, $value),
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Field has wrong value'
        );
    }

    public function isRelationEmpty(): bool
    {
        $selectLocator = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('selectContent'))
            ->build();

        return $this->getHTMLPage()->findAll($selectLocator)->any();
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezobjectrelation';
    }
}
