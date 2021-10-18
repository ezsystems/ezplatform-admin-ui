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

class ObjectStateGroupPage extends Page
{
    /** @var string */
    protected $expectedObjectStateGroupName;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $attributes;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $objectStates;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var mixed */
    private $expectedObjectStateGroupId;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder, Dialog $dialog, Repository $repository)
    {
        parent::__construct($session, $router);
        $this->dialog = $dialog;
        $this->attributes = $tableBuilder->newTable()->withParentLocator($this->getLocator('propertiesTable'))->build();
        $this->objectStates = $tableBuilder->newTable()->withParentLocator($this->getLocator('objectStatesTable'))->build();
        $this->repository = $repository;
    }

    public function editObjectState(string $itemName): void
    {
        $this->objectStates->getTableRow(['Object state name' => $itemName])->edit();
    }

    public function createObjectState(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function setExpectedObjectStateGroupName(string $objectStateGroupName): void
    {
        $this->expectedObjectStateGroupName = $objectStateGroupName;

        /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup[] $objectStateGroups */
        $objectStateGroups = $this->repository->sudo(function () {
            return $this->repository->getObjectStateService()->loadObjectStateGroups();
        });

        foreach ($objectStateGroups as $objectStateGroup) {
            if ($objectStateGroup->getName() === $objectStateGroupName) {
                $this->expectedObjectStateGroupId = $objectStateGroup->id;
            }
        }
    }

    public function hasObjectStates(): bool
    {
        return count($this->objectStates->getColumnValues(['Object state name'])) > 0;
    }

    public function hasAttribute($label, $value): bool
    {
        return $this->attributes->hasElement([$label => $value]);
    }

    public function hasObjectState(string $objectStateName): bool
    {
        return $this->objectStates->hasElement(['Object state name' => $objectStateName]);
    }

    public function deleteObjectState(string $objectStateName)
    {
        $this->objectStates->getTableRow(['Object state name' => $objectStateName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function edit()
    {
        $this->attributes->getTableRowByIndex(0)->edit();
    }

    protected function getRoute(): string
    {
        return sprintf('/state/group/%d', $this->expectedObjectStateGroupId);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('objectStatesTable')))
            ->find($this->getLocator('pageTitle'))
            ->assert()->textEquals(sprintf('Object state group: %s', $this->expectedObjectStateGroupName));
    }

    public function getName(): string
    {
        return 'Object state group';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('propertiesTable', '.ez-container .ibexa-table'),
            new VisibleCSSLocator('objectStatesTable', '[name="object_states_delete"]'),
            new VisibleCSSLocator('createButton', '.ibexa-icon--create'),
            new VisibleCSSLocator('deleteButton', '.ibexa-icon--trash'),
        ];
    }
}
