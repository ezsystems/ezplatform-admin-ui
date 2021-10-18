<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class ObjectStatePage extends Page
{
    /** @var string */
    private $expectedObjectStateName;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var mixed */
    private $expectedObjectStateId;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    public function __construct(Session $session, Router $router, Repository $repository, TableBuilder $tableBuilder)
    {
        parent::__construct($session, $router);
        $this->repository = $repository;
        $this->table = $tableBuilder->newTable()->build();
    }

    public function hasAttribute($label, $value)
    {
        return $this->table->hasElement([$label => $value]);
    }

    public function edit()
    {
        $this->getHTMLPage()->find($this->getLocator('editButton'))->click();
    }

    public function getName(): string
    {
        return 'Object state';
    }

    public function setExpectedObjectStateName(string $objectStateName)
    {
        $this->expectedObjectStateName = $objectStateName;
        $this->getHTMLPage()->setTimeout(3)->waitUntil(function () use ($objectStateName) {
            return $this->getObjectState($objectStateName) !== null;
        }, sprintf('Object state %s was not found', $objectStateName));

        $expectedObjectState = $this->getObjectState($objectStateName);
        $this->expectedObjectStateId = $expectedObjectState->id;
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            sprintf('Object state: %s', $this->expectedObjectStateName),
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    protected function getRoute(): string
    {
        return sprintf('/state/state/%s', $this->expectedObjectStateId);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('editButton', '.ibexa-icon--edit'),
        ];
    }

    private function getObjectState(string $objectStateName): ?ObjectState
    {
        return $this->repository->sudo(static function (Repository $repository) use ($objectStateName) {
            foreach ($repository->getObjectStateService()->loadObjectStateGroups() as $group) {
                foreach ($repository->getObjectStateService()->loadObjectStates($group) as $objectState) {
                    if ($objectState->getName() === $objectStateName) {
                        return $objectState;
                    }
                }
            }

            return null;
        });
    }
}
