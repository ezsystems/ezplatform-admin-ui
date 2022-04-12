<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent;
use Ibexa\AdminUi\Behat\Component\Notification;
use Ibexa\AdminUi\Behat\Component\RightMenu;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;
use Traversable;

class ContentUpdateItemPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\RightMenu */
    private $rightMenu;

    private $pageTitle;

    /** @var \Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent[] */
    private $fieldTypeComponents;

    /** @var \Ibexa\AdminUi\Behat\Component\Notification */
    private $notification;

    public function __construct(
        Session $session,
        Router $router,
        RightMenu $rightMenu,
        Traversable $fieldTypeComponents,
        Notification $notification
    ) {
        parent::__construct($session, $router);
        $this->rightMenu = $rightMenu;
        $this->fieldTypeComponents = iterator_to_array($fieldTypeComponents);
        $this->notification = $notification;
    }

    public function verifyIsLoaded(): void
    {
        if ($this->pageTitle !== null) {
            Assert::assertEquals(
                $this->pageTitle,
                $this->getHTMLPage()
                    ->setTimeout(10)
                    ->find($this->getLocator('pageTitle'))->getText()
            );
        }
        $this->getHTMLPage()->setTimeout(10)->find($this->getLocator('formElement'))->assert()->isVisible();
        $this->rightMenu->verifyIsLoaded();

        // close notification about new draft created successfully if it's still visible
        if ($this->notification->isVisible()) {
            $this->notification->verifyAlertSuccess();
            $this->notification->closeAlert();
        }
    }

    public function setExpectedPageTitle(string $title): void
    {
        $this->pageTitle = $title;
    }

    public function getName(): string
    {
        return 'Content Update';
    }

    public function fillFieldWithValue(string $label, array $value): void
    {
        $this->getField($label)->setValue($value);
    }

    public function close(): void
    {
        $this->getHTMLPage()->find($this->getLocator('closeButton'))->click();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-content-edit-page-title__title'),
            new VisibleCSSLocator('formElement', '[name=ezplatform_content_forms_content_edit]'),
            new VisibleCSSLocator('closeButton', '.ez-content-edit-container__close'),
            new VisibleCSSLocator('fieldLabel', '.ez-field-edit__label-wrapper label.ez-field-edit__label, .ez-field-edit__label-wrapper legend, .ez-card > .card-body > div > div > legend'),
            new VisibleCSSLocator('nthField', '.ez-field-edit:nth-of-type(%s)'),
            new VisibleCSSLocator('activeNthField', '.active .ez-field-edit:nth-of-type(%s)'),
            new VisibleCSSLocator('noneditableFieldClass', 'ez-field-edit--eznoneditable'),
            new VisibleCSSLocator('fieldOfType', '.ez-field-edit--%s'),
            new VisibleCSSLocator('navigationTabs', '.nav-item.ez-tabs__nav-item'),
            new VisibleCSSLocator('autosaveInfo', '.ez-content-edit-page-title__autosave-info'),
            new VisibleCSSLocator('autosaveLastSavedInfo', '.ez-content-edit-page-title__autosave-last-saved'),
        ];
    }

    protected function getRoute(): string
    {
        throw new \Exception('This page cannot be opened on its own!');
    }

    public function getField(string $fieldName): FieldTypeComponent
    {
        $fieldLocator = new VisibleCSSLocator('', sprintf($this->getLocator('nthField')->getSelector(), $this->getFieldPosition($fieldName)));
        $fieldtypeIdentifier = $this->getFieldtypeIdentifier($fieldLocator, $fieldName);

        foreach ($this->fieldTypeComponents as $fieldTypeComponent) {
            if ($fieldTypeComponent->getFieldTypeIdentifier() === $fieldtypeIdentifier) {
                $fieldTypeComponent->setParentLocator($fieldLocator);

                return $fieldTypeComponent;
            }
        }
    }

    protected function getFieldPosition(string $fieldName): int
    {
        $fieldElements = $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('fieldLabel'));

        $foundFields = [];
        foreach ($fieldElements as $fieldPosition => $fieldElement) {
            $fieldText = $fieldElement->getText();
            $foundFields[] = $fieldText;
            if ($fieldText === $fieldName) {
                // +1 because CSS is 1-indexed and arrays are 0-indexed
                return $fieldPosition + 1;
            }
        }

        Assert::fail(sprintf('Field %s not found. Found: %s', $fieldName, implode(',', $foundFields)));
    }

    public function verifyFieldHasValue(string $label, array $fieldData): void
    {
        $this->getField($label)->verifyValueInEditView($fieldData);
    }

    private function getFieldtypeIdentifier(VisibleCSSLocator $fieldLocator, string $fieldName): string
    {
        $isEditable = !$this->getHTMLPage()
            ->find($fieldLocator)
            ->hasClass($this->getLocator('noneditableFieldClass')->getSelector());

        if (!$isEditable) {
            return strtolower($fieldName);
        }

        $fieldClass = $this->getHTMLPage()->find($fieldLocator)->getAttribute('class');
        preg_match('/ez-field-edit--ez[a-z]*/', $fieldClass, $matches);

        return explode('--', $matches[0])[1];
    }

    public function switchToFieldGroup(string $tabName): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('navigationTabs'))
            ->getByCriterion(new ElementTextCriterion($tabName))
            ->click();
    }

    public function verifyFieldCannotBeEditedDueToLimitation(string $fieldName)
    {
        $fieldLocator = new VisibleCSSLocator('', sprintf($this->getLocator('activeNthField')->getSelector(), $this->getFieldPosition($fieldName)));
        $this->getHTMLPage()->find($fieldLocator)->assert()->hasClass('ez-field-edit--disabled');
    }

    public function verifyAutosaveNotificationIsDisplayed(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('autosaveInfo'))
            ->assert()->textContains('Autosave is on.');
    }

    public function verifyAutosaveDraftIsSavedNotificationIsDisplayed(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('autosaveLastSavedInfo'))
            ->assert()->textContains('Last saved draft was on');
    }
}
