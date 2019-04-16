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
use EzSystems\EzPlatformAdminUi\Behat\PageObject\AdminUpdateItemPage;
use PHPUnit\Framework\Exception;

class RolesContext extends BusinessContext
{
    private $tabMapping = [
        'policy' => 'Policies',
        'assignment' => 'Assignments',
    ];

    private $itemTypeToLabelMapping = [
        'users' => 'Select users',
        'groups' => 'Select groups',
    ];

    private $fields = [
        'newPolicySelectList' => 'policy_create_policy',
        'newPolicyAssignmentLimitation' => 'role_assignment_create_sections',
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

        if ('assignment' === $tabName) {
            PageObjectFactory::createPage($this->utilityContext, AdminUpdateItemPage::PAGE_NAME)
                ->adminUpdateForm->fillFieldWithValue('Subtree', 'true');
            $buttonLabel = 'Select Subtree';
        }

        if ($kind === 'subtree') {
            $buttonNo = 1;
        }

        $pageObject = PageObjectFactory::createPage($this->utilityContext, AdminUpdateItemPage::PAGE_NAME);
        $pageObject->adminUpdateForm->clickButton($buttonLabel, $buttonNo);

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
        $rolePage->dialog->verifyVisibility();
        $rolePage->dialog->confirm();
    }

    /**
     * @Then there is a policy :moduleAndFunction with :limitation limitation on the :roleName policies list
     */
    public function thereIsAPolicy(string $moduleAndFunction, string $limitation, string $roleName): void
    {
        $rolePage = PageObjectFactory::createPage($this->utilityContext, RolePage::PAGE_NAME, $roleName);

        if (!$rolePage->isRoleWithLimitationPresent($this->tabMapping['policy'], $moduleAndFunction, $limitation)) {
            throw new Exception(sprintf('Policy "%s" with limitation "%s" not found on the "%s" policies list.', $moduleAndFunction, $limitation, $roleName));
        }
    }

    /**
     * @Then there is no policy :moduleAndFunction with :limitation limitation on the :roleName policies list
     */
    public function thereIsNoPolicy(string $moduleAndFunction, string $limitation, string $roleName): void
    {
        $rolePage = PageObjectFactory::createPage($this->utilityContext, RolePage::PAGE_NAME, $roleName);

        if ($rolePage->isRoleWithLimitationPresent($this->tabMapping['policy'], $moduleAndFunction, $limitation)) {
            throw new Exception(sprintf('Policy "%s" with limitation "%s" found on the "%s" policies list.', $moduleAndFunction, $limitation, $roleName));
        }
    }

    /**
     * @Then there is an assignment :limitation for :userOrGroup on the :roleName assignments list
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
        $this->utilityContext->selectOption($this->fields['newPolicySelectList'], $policyName);
    }

    /**
     * @When I select :limitationName from Sections as role assignment limitation
     */
    public function iSelectSectionLimitation(string $limitationName): void
    {
        PageObjectFactory::createPage($this->utilityContext, AdminUpdateItemPage::PAGE_NAME)
            ->adminUpdateForm->fillFieldWithValue('Sections', 'true');
        $this->utilityContext->selectOption($this->fields['newPolicyAssignmentLimitation'], $limitationName);
    }

    /**
     * @When I assign :itemType to role
     */
    public function iAssignToRole(string $itemType, TableNode $items): void
    {
        $pageObject = PageObjectFactory::createPage($this->utilityContext, AdminUpdateItemPage::PAGE_NAME);
        $pageObject->adminUpdateForm->clickButton($this->itemTypeToLabelMapping[$itemType]);

        $udw = ElementFactory::createElement($this->utilityContext, UniversalDiscoveryWidget::ELEMENT_NAME);
        $udw->verifyVisibility();

        foreach ($items->getHash() as $item) {
            $udw->selectContent($item['path']);
        }

        $udw->confirm();
    }
}
