<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

class BookmarksPage extends Page
{
    /** \Ibexa\AdminUi\Behat\Component\Table\TableInterface */
    private $table;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('pageTitle'))->assert()->textEquals('Bookmarks');
    }

    public function isBookmarked(string $contentName): bool
    {
        return $this->table->hasElement(['Name' => $contentName]);
    }

    public function goToItem(string $contentName)
    {
        $this->table->getTableRow(['Name' => $contentName])->goToItem();
    }

    public function edit(string $contentName)
    {
        $this->table->getTableRow(['Name' => $contentName])->edit();
    }

    public function delete(string $contentName)
    {
        $this->table->getTableRow(['Name' => $contentName])->select();

        $this->getHTMLPage()->find(new VisibleCSSLocator('deleteButton', 'button#bookmark_remove_remove'))->click();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title__content-name'),
            new VisibleCSSLocator('itemName', 'tr .ez-table__cell--after-icon'),
        ];
    }

    public function getName(): string
    {
        return 'Bookmarks';
    }

    protected function getRoute(): string
    {
        return 'bookmark/list';
    }
}
