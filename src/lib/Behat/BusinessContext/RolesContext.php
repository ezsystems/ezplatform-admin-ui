<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
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

    /**
     * @When I start assigning users and groups to :roleName from :pageName page
     */
    public function iStartAssigningTo(string $roleName, string $pageName): void
    {
        $pageObject = PageObjectFactory::createPage($this->utilityContext, $pageName, $roleName);
        $pageObject->navLinkTabs->goToTab($this->tabMapping['assignment']);
        $pageObject->adminLists[$this->tabMapping['assignment']]->clickAssignButton();
    }

    /**
     * @When I :buttonLabel :itemPath through UDW
     */
    public function iSelectLocationThroughUDW(string $buttonLabel, string $itemPath): void
    {
        $pageObject = PageObjectFactory::createPage($this->utilityContext, UpdateItemPage::PAGE_NAME);
        $pageObject->updateForm->clickButton($buttonLabel);

        $udw = new UniversalDiscoveryWidget($this->utilityContext);
        $udw->verifyVisibility();
        $udw->selectContent($itemPath);
        $udw->confirm();
    }

    /**
     * @When I delete :itemType :itemName from :roleName role
     * @When I delete :itemType from :roleName role
     */
    public function iDeleteManyFromRole(string $itemType, string $roleName, ?string $itemName = null, ?TableNode $settings = null): void
    {
        $rolePage = PageObjectFactory::createPage($this->utilityContext, RolePage::PAGE_NAME, $roleName);
        $rolePage->navLinkTabs->goToTab($this->tabMapping[$itemType]);
        $adminList = $rolePage->adminLists[$this->tabMapping[$itemType]];

        $elements = ($settings === null) ? [['item' => $itemName]] : $settings->getHash();
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
        foreach ($actualPoliciesList as $policy) {
            if (
                $policy['Module'] === explode('/', $moduleAndFunction)[0] &&
                $policy['Function'] === explode('/', $moduleAndFunction)[1] &&
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
     * @Then There's a assignment :limitation for :userOrGroup on the :roleName assignments list
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
     * @Then There's policies on the :roleName policies list
     */
    public function thereArePolicies(string $roleName, TableNode $settings): void
    {
        $policies = $settings->getHash();
        foreach ($policies as $policy) {
            $this->thereIsAPolicy($policy['policy'], $policy['limitation'], $roleName);
        }
    }

    /**
     * @Then There's assignments on the :roleName assignments list
     */
    public function thereAreAssignments(string $roleName, TableNode $settings): void
    {
        $policies = $settings->getHash();
        foreach ($policies as $policy) {
            $this->thereIsAnAssignment($policy['limitation'], $policy['user/group'], $roleName);
        }
    }
}
