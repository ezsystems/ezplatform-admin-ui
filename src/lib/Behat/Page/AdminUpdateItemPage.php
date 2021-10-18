<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Locator\XPathLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class AdminUpdateItemPage extends Page
{
    /** @var \Ibexa\AdminUi\Behat\Component\ContentActionsMenu */
    protected $contentActionsMenu;

    public function __construct(Session $session, Router $router, ContentActionsMenu $contentActionsMenu)
    {
        parent::__construct($session, $router);
        $this->contentActionsMenu = $contentActionsMenu;
    }

    public function getFieldValue($label)
    {
        return $this->getField($label)->getValue();
    }

    protected function getRoute(): string
    {
        throw new \Exception('Update Page cannot be opened on its own!');
    }

    public function getName(): string
    {
        return 'Admin item update';
    }

    public function fillFieldWithValue(string $fieldName, $value): void
    {
        $field = $this->getField($fieldName);
        $fieldType = $field->getAttribute('type');

        $this->getHTMLPage()->setTimeout(3)->waitUntil(static function () use ($field, $fieldType, $value) {
            $field->setValue($value);

            return $fieldType !== 'text' || $value === $field->getValue();
        }, sprintf('Failed to set correct value in input field. Expected: %s. Actual: %s', $value, $field->getValue()));
    }

    public function clickButton(string $label): void
    {
        $button = $this->getHTMLPage()
            ->findAll($this->getLocator('button'))
            ->getByCriterion(new ElementTextCriterion($label));

        $button->mouseOver();
        $button->find(new VisibleCSSLocator('label', 'span'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->contentActionsMenu->verifyIsLoaded();
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('formElement'))->isVisible());
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('formElement', '.ibexa-main-container '),
            new VisibleCSSLocator('closeButton', '.ez-content-edit-container__close'),
            new VisibleCSSLocator('button', '.container button'),
            new VisibleCSSLocator('fieldInput', 'input'),
        ];
    }

    private function getField(string $fieldName): ElementInterface
    {
        return $this->getHTMLPage()
            ->findAll(new XPathLocator('input', '//label/..'))
            ->getByCriterion(new ElementTextCriterion($fieldName))
            ->find(new VisibleCSSLocator('input', 'input'));
    }
}
