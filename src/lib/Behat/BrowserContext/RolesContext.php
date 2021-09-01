<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\RolePage;
use Ibexa\AdminUi\Behat\Page\RolesPage;
use Ibexa\AdminUi\Behat\Page\RoleUpdatePage;
use PHPUnit\Framework\Assert;

class RolesContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Page\RolesPage */
    private $rolesPage;

    /** @var \Ibexa\AdminUi\Behat\Page\RolePage */
    private $rolePage;

    /** @var \Ibexa\AdminUi\Behat\Page\RoleUpdatePage */
    private $roleUpdatePage;

    public function __construct(
        RolesPage $rolesPage,
        RolePage $rolePage,
        RoleUpdatePage $roleUpdatePage
    ) {
        $this->rolePage = $rolePage;
        $this->roleUpdatePage = $roleUpdatePage;
        $this->rolesPage = $rolesPage;
    }

    /**
     * @When I start assigning users and groups from Role page
     */
    public function iStartAssigningUsersToRole(): void
    {
        $this->rolePage->startAssigningUsers();
    }

    /**
     * @When I delete assignment from :roleName role
     */
    public function iDeleteAssignmentsFromRole(string $roleName, TableNode $items): void
    {
        $itemNames = array_column($items->getHash(), 'item');

        $this->rolePage->setExpectedRoleName($roleName);
        $this->rolePage->deleteAssignments($itemNames);
    }

    /**
     * @When I delete policy from :roleName role
     */
    public function iDeleteAPolicyFromRole(string $roleName, TableNode $items): void
    {
        $itemNames = array_column($items->getHash(), 'item');

        $this->rolePage->setExpectedRoleName($roleName);
        $this->rolePage->deletePolicies($itemNames);
    }

    /**
     * @Then there is a policy :moduleAndFunction with :limitation limitation on the :roleName policies list
     */
    public function thereIsAPolicy(string $moduleAndFunction, string $limitation, string $roleName): void
    {
        $this->rolePage->setExpectedRoleName($roleName);
        Assert::assertTrue($this->rolePage->isRoleWithLimitationPresent($moduleAndFunction, $limitation));
    }

    /**
     * @Then there is no policy :moduleAndFunction with :limitation limitation on the :roleName policies list
     */
    public function thereIsNoPolicy(string $moduleAndFunction, string $limitation, string $roleName): void
    {
        $this->rolePage->setExpectedRoleName($roleName);
        Assert::assertFalse($this->rolePage->isRoleWithLimitationPresent($moduleAndFunction, $limitation));
    }

    /**
     * @Then there are policies on the :roleName policies list
     */
    public function thereArePolicies(string $roleName, TableNode $settings): void
    {
        $policies = $settings->getHash();
        foreach ($policies as $policy) {
            $this->thereIsAPolicy($policy['policy'], $policy['limitation'], $roleName);
        }
    }

    /**
     * @Then there are assignments on the :roleName assignments list
     */
    public function thereAreAssignments(TableNode $expectedAssignments): void
    {
        $this->rolePage->verifyAssignments($expectedAssignments->getHash());
    }

    /**
     * @When I select policy :policyName
     */
    public function iSelectPolicy(string $policyName): void
    {
        $this->roleUpdatePage->selectPolicy($policyName);
    }

    /**
     * @When I select :limitationName from Sections as role assignment limitation
     */
    public function iSelectSectionLimitation(string $limitationName): void
    {
        $this->roleUpdatePage->assignSectionLimitation($limitationName);
    }

    /**
     * @When I assign :itemType to role
     */
    public function iAssignToRole(string $itemType, TableNode $items): void
    {
        $itemPaths = array_column($items->getHash(), 'path');
        $this->roleUpdatePage->assign($itemPaths, $itemType);
    }

    /**
     * @When I select limitation for :selectName
     */
    public function iSelectOptionsFrom(string $selectName, TableNode $options): void
    {
        $values = array_column($options->getHash(), 'option');
        $this->roleUpdatePage->selectLimitationValues($selectName, $values);
    }

    /**
     * @When I create a new Role
     */
    public function createNewRole(): void
    {
        $this->rolesPage->create();
    }

    /**
     * @When I start creating a new Policy
     */
    public function createNewPolicy(): void
    {
        $this->rolePage->createPolicy();
    }

    /**
     * @Given there's no :roleName Role on Roles list
     */
    public function thereSNoRoleOnRoleList(string $roleName)
    {
        Assert::assertFalse($this->rolesPage->isRoleOnTheList($roleName));
    }

    /**
     * @Given I delete Role :roleName
     */
    public function deleteRoleNamed(string $roleName)
    {
        $this->rolesPage->deleteRole($roleName);
    }

    /**
     * @Given there's a :roleName Role on Roles list
     */
    public function thereARoleOnRoleList(string $roleName)
    {
        Assert::assertTrue($this->rolesPage->isRoleOnTheList($roleName));
    }

    /**
     * @Then I should be on :roleName Role page
     */
    public function iShouldBeOnRolePage(string $roleName)
    {
        $this->rolePage->setExpectedRoleName($roleName);
        $this->rolePage->verifyIsLoaded();
    }

    /**
     * @Then Policies list is empty
     */
    public function policiesListIsEmpty()
    {
        Assert::assertFalse($this->rolePage->hasPolicies());
    }

    /**
     * @Then I start assigning to :roleName from Roles page
     */
    public function startAssigning(string $roleName)
    {
        $this->rolesPage->startAssinging($roleName);
    }

    /**
     * @Then Assignments list is empty
     */
    public function assignmentslistIsEmpty()
    {
        Assert::assertFalse($this->rolePage->hasAssignments());
    }

    /**
     * @Then I edit :roleName from Roles list
     */
    public function editRoleFromRolesList(string $roleName)
    {
        $this->rolesPage->editRole($roleName);
    }

    /**
     * @Then I open :roleName Role page in admin SiteAccess
     */
    public function openRolePage(string $roleName)
    {
        $this->rolePage->setExpectedRoleName($roleName);
        $this->rolePage->open('admin');
        $this->rolePage->verifyIsLoaded();
    }

    /**
     * @Then I start editing the policy :policyName :functionName
     */
    public function editPolicy(string $policyName, string $functionName)
    {
        $this->rolePage->editPolicy($policyName, $functionName);
    }

    /**
     * @When I select limitation :itemPath for assignment through UDW
     */
    public function iSelectLimitationForAssignmentThroughUDW(string $itemPath): void
    {
        $this->roleUpdatePage->selectLimitationForAssignment($itemPath);
    }

    /**
     * @When I select subtree limitation :itemPath for policy through UDW
     */
    public function iSelectSubtreeLimitationForPolicyThroughUDW(string $itemPath): void
    {
        $this->roleUpdatePage->selectSubtreeLimitationForPolicy($itemPath);
    }
}
