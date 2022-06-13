<?php

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Locator\XPathLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;
use Ibexa\Behat\Browser\Element\ElementInterface;

class SegmentsGroupPage extends Page
{
    public function __construct(Session $session, Router $router)
    {
        parent::__construct($session, $router);
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

    public function getFieldValue($label)
    {
        return $this->getField($label)->getValue();
    }

    private function getField(string $fieldName): ElementInterface
    {
        return $this->getHTMLPage()
            ->findAll(new XPathLocator('input', '//label/..'))
            ->getByCriterion(new ElementTextCriterion($fieldName))
            ->find(new VisibleCSSLocator('input', 'input'));
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createSegmentGroupButton', '.ez-icon--create'),
            new VisibleCSSLocator('fieldInput','input'),
            new VisibleCSSLocator('segmentCreateButton','#segment_group_create_create')
        ];
    }

    public function openSegmentGroupCreationWindow(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createSegmentGroupButton'))->click();
    }

    public function clickOnCreateButton(): void
    {
        $this->getHTMLPage()->find($this->getLocator('segmentCreateButton'))->click();
    }

    public function getName(): string
    {
        return 'Segmentation';
    }

    public function verifyIsLoaded(): void
    {

    }

    public function verifyComponentIsLoaded(): void
    {

    }

    protected function getRoute(): string
    {
        return 'segmentation/group/list';
    }

}