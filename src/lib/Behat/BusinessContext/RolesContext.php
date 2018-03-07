<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\RolePage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\UpdateItemPage;
use PHPUnit\Framework\Exception;

class RolesContext extends BusinessContext
{
    private $tabMapping = [
        'policy' => 'Policies',
        'assignment' => 'Assignments',
    ];

    private $fields = [
        'newPolicySelectList' => 'policy_create_policy',
        'newPolicyAssignmentLimitation' => 'role_assignment_create_sections'
    ];

    /**
     * @When I start assigning users and groups to :roleName from role page
     */
    public function iStartAssigningTo(string $roleName): void
    {
        $pageObject = PageObjectFactory::createPage($this->utilityContext, 'Role', $roleName);
        $pageObject->navLinkTabs->goToTab($this->tabMapping['assignment']);
        $pageObject->adminLists[$this->tabMapping['assignment']]->clickAssignButton();
    }

    /**
     * @When I select limitation :itemPath for :tabName through UDW
     * @When I select :kind limitation :itemPath for :tabName through UDW
     */
    public function iSelectSubtreeLimitationThroughUDW(string $itemPath, string $tabName, ?string $kind = null): void
    {
        $buttonLabel = 'Select locations';
        $buttonNo = 0;

        if('assignment' === $tabName) {
            PageObjectFactory::createPage($this->utilityContext, UpdateItemPage::PAGE_NAME)
                ->updateForm->fillFieldWithValue('Subtree', 'true');
            $buttonLabel = 'Select Subtree';
        }

        if($kind === 'subtree') {
            $buttonNo = 1;
        }

        $pageObject = PageObjectFactory::createPage($this->utilityContext, UpdateItemPage::PAGE_NAME);
        $pageObject->updateForm->clickButton($buttonLabel, $buttonNo);

        $udw = ElementFactory::createElement($this->utilityContext, UniversalDiscoveryWidget::ELEMENT_NAME);
        $udw->verifyVisibility();
        $udw->selectContent($itemPath);
        $udw->confirm();
    }

    /**
     * @When I delete :itemType from :roleName role
     */
    public function iDeleteManyFromRole(string $itemType, string $roleName, TableNode $settings): void
    {
        $rolePage = PageObjectFactory::createPage($this->utilityContext, RolePage::PAGE_NAME, $roleName);
        $rolePage->navLinkTabs->goToTab($this->tabMapping[$itemType]);
        $adminList = $rolePage->adminLists[$this->tabMapping[$itemType]];

        $elements = $settings->getHash();
        foreach ($elements as $element) {
            $adminList->table->selectListElement($element['item']);
        }

        $adminList->clickTrashButton();
        $dialog = $rolePage->dialogs[$this->tabMapping[$itemType]];
        $dialog->verifyVisibility();
        $dialog->confirm();
    }

    /**
     * @Then There's a policy :moduleAndFunction with :limitation limitation on the :roleName policies list
     */
    public function thereIsAPolicy(string $moduleAndFunction, string $limitation, string $roleName): void
    {
        $rolePage = PageObjectFactory::createPage($this->utilityContext, RolePage::PAGE_NAME, $roleName);
        $rolePage->navLinkTabs->goToTab($this->tabMapping['policy']);
        $adminList = $rolePage->adminLists[$this->tabMapping['policy']];
        $actualPoliciesList = $adminList->table->getTableHash();
        $policyExists = false;
        $expectedModule = explode('/', $moduleAndFunction)[0];
        $expectedFunction = explode('/', $moduleAndFunction)[1];
        foreach ($actualPoliciesList as $policy) {
            if (
                $policy['Module'] === $expectedModule &&
                $policy['Function'] === $expectedFunction &&
                strpos($policy['Limitations'], $limitation) !== false
            ) {
                $policyExists = true;
            }
        }

        if (!$policyExists) {
            throw new Exception(sprintf('Policy "%s" with limitation "%s" not found.', $moduleAndFunction, $limitation));
        }
    }

    /**
     * @Then There's an assignment :limitation for :userOrGroup on the :roleName assignments list
     */
    public function thereIsAnAssignment(string $limitation, string $userOrGroup, string $roleName): void
    {
        $rolePage = PageObjectFactory::createPage($this->utilityContext, RolePage::PAGE_NAME, $roleName);
        $rolePage->navLinkTabs->goToTab($this->tabMapping['assignment']);
        $adminList = $rolePage->adminLists[$this->tabMapping['assignment']];
        $actualAssignmentList = $adminList->table->getTableHash();
        $assignmentExists = false;
        foreach ($actualAssignmentList as $policy) {
            if ($policy['User/Group'] === $userOrGroup && (strpos($policy['Limitation'], $limitation) !== false)) {
                $assignmentExists = true;
            }
        }

        if (!$assignmentExists) {
            throw new Exception(sprintf('Assignment to "%s" with limitation "%s" not found.', $userOrGroup, $limitation));
        }
    }

    /**
     * @Then There are policies on the :roleName policies list
     */
    public function thereArePolicies(string $roleName, TableNode $settings): void
    {
        $policies = $settings->getHash();
        foreach ($policies as $policy) {
            $this->thereIsAPolicy($policy['policy'], $policy['limitation'], $roleName);
        }
    }

    /**
     * @Then There are assignments on the :roleName assignments list
     */
    public function thereAreAssignments(string $roleName, TableNode $settings): void
    {
        $policies = $settings->getHash();
        foreach ($policies as $policy) {
            $this->thereIsAnAssignment($policy['limitation'], $policy['user/group'], $roleName);
        }
    }

    /**
     * @When I select policy :policyName
     */
    public function iSelectPolicy(string $policyName): void
    {
        $this->utilityContext->selectOption($this->fields['newPolicySelectList'],$policyName);
    }

    /**
     * @When I select :limitationName from Sections as role assignment limitation
     */
    public function iSelectSectionLimitation(string $limitationName): void
    {
        PageObjectFactory::createPage($this->utilityContext, UpdateItemPage::PAGE_NAME)
            ->updateForm->fillFieldWithValue('Sections', 'true');
        $this->utilityContext->selectOption($this->fields['newPolicyAssignmentLimitation'], $limitationName);
    }
}
