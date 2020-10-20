<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminUpdateForm;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use PHPUnit\Framework\Assert;

class AdminUpdateItemPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Admin Item Update';

    /**
     * @var AdminUpdateForm
     */
    public $adminUpdateForm;

    /**
     * @var RightMenu
     */
    public $rightMenu;

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/contenttypegroup';
        $this->adminUpdateForm = ElementFactory::createElement($this->context, AdminUpdateForm::ELEMENT_NAME);
        $this->rightMenu = ElementFactory::createElement($this->context, RightMenu::ELEMENT_NAME);
        $this->pageTitle = 'Editing';
        $this->fields = [
            'limitationField' => '.ez-update-policy__action-wrapper',
            'limitationDropdown' => '.ez-custom-dropdown__selection-info',
            'limitationDropdownOption' => 'ul:not(.ez-custom-dropdown__items--hidden) .ez-custom-dropdown__item',
            'limitationDropdownOptionRemove' => '.ez-custom-dropdown__remove-selection',
        ];
    }

    public function verifyElements(): void
    {
        $this->rightMenu->verifyVisibility();
        $this->adminUpdateForm->verifyVisibility();
    }

    public function verifyTitle(): void
    {
        Assert::assertContains(
            $this->pageTitle,
            $this->getPageTitle(),
            'Wrong page title.'
        );
    }

    public function selectLimitationValues(string $selectName, array $values): void
    {
        try {
            $baseElement = $this->context->getElementByText($selectName, $this->fields['limitationField'], '.ez-label');
            $currentlySelectedElements = $this->context->findAllElements($this->fields['limitationDropdownOptionRemove'], $baseElement);

            for ($i = 0; $i < count($currentlySelectedElements); ++$i) {
                $baseElement = $this->context->getElementByText($selectName, $this->fields['limitationField'], '.ez-label');
                $currentlySelectedElement = $this->context->findElement($this->fields['limitationDropdownOptionRemove'], $this->defaultTimeout, $baseElement);
                $currentlySelectedElement->click();
            }
        } catch (\Exception $e) {
            // no need to remove current selection
        }

        $baseElement = $this->context->getElementByText($selectName, $this->fields['limitationField'], '.ez-label');
        $baseElement->find('css', $this->fields['limitationDropdown'])->click();

        foreach ($values as $value) {
            $this->context->getElementByText($value, $this->fields['limitationDropdownOption'])->click();
        }

        $baseElement = $this->context->getElementByText($selectName, $this->fields['limitationField'], '.ez-label');
        $baseElement->find('css', $this->fields['limitationDropdown'])->click();
    }
}
