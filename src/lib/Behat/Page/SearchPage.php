<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use PHPUnit\Framework\Assert;

class SearchPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableInterface */
    private $table;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder
            ->newTable()
            ->withParentLocator($this->getLocator('table'))
            ->withEmptyLocator($this->getLocator('emptyTable'))
            ->build()
        ;
    }

    public function search(string $contentItemName): void
    {
        $this->getHTMLPage()->find($this->getLocator('inputField'))->setValue($contentItemName);
        $this->getHTMLPage()->find($this->getLocator('buttonSearch'))->click();
        $this->verifyIsLoaded();
        $this->getHTMLPage()->find($this->getLocator('table'))->assert()->isVisible();
    }

    public function isElementInResults(array $elementData): bool
    {
        return $this->table->hasElement($elementData);
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Search',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Search';
    }

    protected function getRoute(): string
    {
        return '/search';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('inputField', '.ez-search-form #search_query'),
            new VisibleCSSLocator('buttonSearch', '.ibexa-btn--search'),
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('table', '.ez-content-container table.table'),
            new VisibleCSSLocator('emptyTable', '.ibexa-table-header__headline'),
        ];
    }
}
