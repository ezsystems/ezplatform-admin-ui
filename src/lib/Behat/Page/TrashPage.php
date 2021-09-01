<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class TrashPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    public $dialog;

    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \Ibexa\AdminUi\Behat\Component\ContentActionsMenu */
    private $contentActionsMenu;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    public function __construct(
        Session $session,
        Router $router,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        Dialog $dialog,
        ContentActionsMenu $contentActionsMenu,
        TableBuilder $tableBuilder
    ) {
        parent::__construct($session, $router);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->dialog = $dialog;
        $this->contentActionsMenu = $contentActionsMenu;
        $this->table = $tableBuilder->newTable()->build();
    }

    public function hasElement(string $itemType, string $itemName): bool
    {
        return $this->table->hasElement(['Name' => $itemName, 'Content type' => $itemType]);
    }

    public function isEmpty(): bool
    {
        return $this->table->isEmpty();
    }

    public function restoreSelectedNewLocation(string $pathToContent)
    {
        $this->getHTMLPage()->find($this->getLocator('restoreUnderNewLocationButton'))->click();
        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($pathToContent);
        $this->universalDiscoveryWidget->confirm();
    }

    public function emptyTrash()
    {
        $this->contentActionsMenu->clickButton('Empty Trash');
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function deleteSelectedItems()
    {
        $this->getHTMLPage()->find($this->getLocator('trashButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function select(array $parameters)
    {
        $this->table->getTableRow($parameters)->select();
    }

    public function restoreSelectedItems()
    {
        $this->getHTMLPage()->find($this->getLocator('restoreButton'))->click();
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Trash',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Trash';
    }

    protected function getRoute(): string
    {
        return 'trash/list';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('restoreButton', '#trash_item_restore_restore'),
            new VisibleCSSLocator('trashButton', '#delete-trash-items'),
            new VisibleCSSLocator('restoreUnderNewLocationButton', '#trash_item_restore_location_select_content'),
        ];
    }
}
