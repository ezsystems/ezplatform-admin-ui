<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Exception;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\AdminUi\Behat\Component\RightMenu;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;

class RoleUpdatePage extends AdminUpdateItemPage
{
    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    public function __construct(Session $session, Router $router, RightMenu $rightMenu, UniversalDiscoveryWidget $universalDiscoveryWidget)
    {
        parent::__construct($session, $router, $rightMenu);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
    }

    public function selectLimitationValues(string $selectName, array $values): void
    {
        try {
            $currentlySelectedElementsCount = $this->getHTMLPage()
                ->findAll($this->getLocator('limitationField'))
                ->getByCriterion(new ChildElementTextCriterion($this->getLocator('labelSelector'), $selectName))
                ->findAll($this->getLocator('limitationDropdownOptionRemove'))
                ->count()
            ;

            for ($i = 0; $i < $currentlySelectedElementsCount; ++$i) {
                $this->getHTMLPage()
                    ->findAll($this->getLocator('limitationField'))
                    ->getByCriterion(new ChildElementTextCriterion($this->getLocator('labelSelector'), $selectName))
                    ->find($this->getLocator('limitationDropdownOptionRemove'))
                    ->click()
                ;
            }
        } catch (Exception $e) {
            // no need to remove current selection
        }

        $this->getHTMLPage()
            ->findAll($this->getLocator('limitationField'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('labelSelector'), $selectName))
            ->find($this->getLocator('limitationDropdown'))
            ->click()
        ;

        foreach ($values as $value) {
            $this->getHTMLPage()->findAll($this->getLocator('limitationDropdownOption'))->getByCriterion(new ElementTextCriterion($value))->click();
        }

        $this->getHTMLPage()
            ->findAll($this->getLocator('limitationField'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('labelSelector'), $selectName))
            ->find($this->getLocator('limitationDropdown'))
            ->click()
        ;
    }

    public function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                new VisibleCSSLocator('limitationField', '.ez-update-policy__action-wrapper'),
                new VisibleCSSLocator('limitationDropdown', '.ez-custom-dropdown__selection-info'),
                new VisibleCSSLocator('limitationDropdownOption', 'ul:not(.ez-custom-dropdown__items--hidden) .ez-custom-dropdown__item'),
                new VisibleCSSLocator('limitationDropdownOptionRemove', '.ez-custom-dropdown__remove-selection'),
                new VisibleCSSLocator('labelSelector', '.ez-label'),
                new VisibleCSSLocator('policyAssignmentSelect', '#role_assignment_create_sections'),
                new VisibleCSSLocator('newPolicySelectList', '#policy_create_policy'),
            ]
        );
    }

    public function assign(array $itemPaths, string $itemType)
    {
        $itemTypeToLabelMapping = [
            'users' => 'Select Users',
            'groups' => 'Select User Groups',
        ];

        $this->clickButton($itemTypeToLabelMapping[$itemType]);
        $this->universalDiscoveryWidget->verifyIsLoaded();

        foreach ($itemPaths as $itemPath) {
            $this->universalDiscoveryWidget->selectContent($itemPath);
        }

        $this->universalDiscoveryWidget->confirm();
    }

    public function assignSectionLimitation(string $limitationName): void
    {
        $this->fillFieldWithValue('Sections', true);
        $this->getHTMLPage()->find($this->getLocator('policyAssignmentSelect'))->selectOption($limitationName);
    }

    public function selectLimitationForAssignment(string $itemPath)
    {
        $this->fillFieldWithValue('Subtree', 'true');
        $this->clickButton('Select Subtree');
        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($itemPath);
        $this->universalDiscoveryWidget->confirm();
    }

    public function selectSubtreeLimitationForPolicy(string $itemPath)
    {
        $buttons = $this->getHTMLPage()
            ->findAll($this->getLocator('button'))
            ->filterBy(new ElementTextCriterion('Select Locations'))
            ->toArray()
        ;

        $buttons[1]->click();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($itemPath);
        $this->universalDiscoveryWidget->confirm();
    }

    public function selectPolicy(string $policyName)
    {
        $this->getHTMLPage()->find($this->getLocator('newPolicySelectList'))->selectOption($policyName);
    }
}
