<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use eZ\Publish\API\Repository\Repository;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

class SectionPage extends Page
{
    /** @var string locator for container of Content list */
    public $secondListContainerLocator = 'section:nth-of-type(2)';

    /** @var \Ibexa\AdminUi\Behat\Component\AdminList[] */
    public $adminLists;

    /** @var \Ibexa\AdminUi\Behat\Component\AdminList */
    public $adminList;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog[] */
    public $dialogs;

    /** @var string */
    private $expectedSectionName;

    /** @var int */
    private $expectedSectionId;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableInterface */
    private $contentItemsTable;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableInterface */
    private $sectionInformationTable;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(
        Session $session, Router $router,
        TableBuilder $tableBuilder,
        Dialog $dialog,
        Repository $repository)
    {
        parent::__construct($session, $router);
        $this->contentItemsTable = $tableBuilder->newTable()->withParentLocator($this->getLocator('contentItemsTable'))->build();
        $this->sectionInformationTable = $tableBuilder->newTable()->withParentLocator($this->getLocator('sectionInfoTable'))->build();
        $this->dialog = $dialog;
        $this->repository = $repository;
    }

    public function isContentListEmpty(): bool
    {
        return $this->contentItemsTable->isEmpty();
    }

    public function hasProperties(array $sectionProperties): bool
    {
        return $this->sectionInformationTable->hasElement($sectionProperties);
    }

    public function hasAssignedItem(array $elementData): bool
    {
        return $this->contentItemsTable->hasElement($elementData);
    }

    public function edit()
    {
        $this->sectionInformationTable->getTableRow(['Name' => $this->expectedSectionName])->edit();
    }

    public function assignContentItems()
    {
        $this->getHTMLPage()->find($this->getLocator('assignButton'))->click();
    }

    public function hasAssignedItems(): bool
    {
        return !$this->contentItemsTable->isEmpty();
    }

    public function delete()
    {
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/section/view/%d', $this->expectedSectionId
        );
    }

    public function setExpectedSectionName(string $sectionName): void
    {
        $this->expectedSectionName = $sectionName;

        $sections = $this->repository->sudo(static function (Repository $repository) {
            return $repository->getSectionService()->loadSections();
        });

        foreach ($sections as $section) {
            if ($section->name === $sectionName) {
                $this->expectedSectionId = $section->id;

                return;
            }
        }
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('contentItemsTable')))
            ->find($this->getLocator('pageTitle'))
            ->assert()->textEquals(sprintf('Section: %s', $this->expectedSectionName));
    }

    public function getName(): string
    {
        return 'Section';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('contentItemsTable', '.ez-container ~ .ez-container'),
            new VisibleCSSLocator('assignButton', '#section_content_assign_locations_select_content'),
            new VisibleCSSLocator('sectionInfoTable', '.ez-container .ibexa-table'),
            new VisibleCSSLocator('deleteButton', 'button[data-bs-original-title="Delete Section"]'),
        ];
    }
}
