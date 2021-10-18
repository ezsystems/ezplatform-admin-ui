<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use eZ\Publish\API\Repository\ContentTypeService;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

class ContentTypeGroupPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\AdminList */
    protected $adminList;

    /** @var string */
    protected $expectedName;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var mixed */
    private $contentTypeGroupId;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    public function __construct(Session $session, Router $router, ContentTypeService $contentTypeService, TableBuilder $tableBuilder, Dialog $dialog)
    {
        parent::__construct($session, $router);
        $this->contentTypeService = $contentTypeService;
        $this->table = $tableBuilder->newTable()->withParentLocator($this->getLocator('tableContainer'))->build();
        $this->dialog = $dialog;
    }

    public function hasContentTypes(): bool
    {
        return $this->getHTMLPage()->findAll($this->getLocator('tableItem'))->any();
    }

    public function edit(string $contentTypeName): void
    {
        $this->table->getTableRow(['Name' => $contentTypeName])->edit();
    }

    public function goTo(string $contentTypeName): void
    {
        $this->table->getTableRow(['Name' => $contentTypeName])->goToItem();
    }

    public function createNew(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function isContentTypeOnTheList($contentTypeName): bool
    {
        return $this->table->hasElement(['Name' => $contentTypeName]);
    }

    public function delete(string $contentTypeName)
    {
        $contentTypeLabelLocator = $this->getLocator('contentTypeLabel');
        $listElement = $this->getHTMLPage()
            ->findAll($contentTypeLabelLocator)
            ->getByCriterion(new ElementTextCriterion($contentTypeName));
        usleep(1000000); //TODO : refactor after redesign
        $listElement->mouseOver();
        $this->table->getTableRow(['Name' => $contentTypeName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function hasAssignedContentItems(string $contentTypeGroupName): bool
    {
        return $this->table->getTableRow(['Name' => $contentTypeGroupName])->getCellValue('Number of Content Types') > 0;
    }

    protected function getRoute(): string
    {
        return sprintf('/contenttypegroup/%d', $this->contentTypeGroupId);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('pageTitle'))
            ->assert()->textEquals($this->expectedName);
        $this->getHTMLPage()
            ->find($this->getLocator('listHeader'))
            ->assert()->textEquals(sprintf("Content Types in '%s'", $this->expectedName));
    }

    public function setExpectedContentTypeGroupName(string $expectedName)
    {
        $this->expectedName = $expectedName;
        $groups = $this->contentTypeService->loadContentTypeGroups();

        foreach ($groups as $group) {
            if ($group->identifier === $expectedName) {
                $this->contentTypeGroupId = $group->id;

                return;
            }
        }
    }

    public function getName(): string
    {
        return 'Content Type group';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('createButton', '.ibexa-icon--create'),
            new VisibleCSSLocator('listHeader', '.ibexa-table-header .ibexa-table-header__headline, header .ibexa-table__headline, header h5'),
            new VisibleCSSLocator('tableContainer', '.ez-container'),
            new VisibleCSSLocator('deleteButton', '.ibexa-icon--trash,button[data-bs-original-title^="Delete"]'),
            new VisibleCSSLocator('tableItem', '.ez-main-container tbody tr'),
            new VisibleCSSLocator('contentTypeLabel', '.ibexa-table__cell > a'),
        ];
    }
}
