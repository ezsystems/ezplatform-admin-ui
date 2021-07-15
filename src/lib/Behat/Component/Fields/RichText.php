<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Exception;

class RichText extends FieldTypeComponent
{
    private $setAlloyEditorValueScript = 'CKEDITOR.instances.%s.setData(\'%s\')';
    private $insertAlloyEditorValueScript = 'CKEDITOR.instances.%s.insertText(\'%s\')';
    private $executeAlloyEditorScript = 'CKEDITOR.instances.%s.execCommand(\'%s\')';
    protected $richtextId;
    protected const ALLOWED_STYLE_OPTIONS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'pre'];
    protected const ALLOWED_MOVE_OPTIONS = ['up', 'down'];

    public function setValue(array $parameters): void
    {
        $this->getFieldInput();
        $this->getSession()->getDriver()->executeScript(
            sprintf($this->setAlloyEditorValueScript, $this->richtextId, $parameters['value'])
        );
    }

    public function getValue(): array
    {
        $fieldInput = $this->getFieldInput();

        return [$fieldInput->getText()];
    }

    public function openElementsToolbar(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addButton'))->click();
        usleep(200 * 1000); // wait until the transition animations ends
        Assert::assertTrue($this->getHTMLPage()->setTimeout(3)->find($this->getLocator('toolbarButton'))->isVisible());
    }

    public function changeStyle(string $style): void
    {
        if (!in_array($style, self::ALLOWED_STYLE_OPTIONS)) {
            throw new Exception(sprintf('Unsupported style: %s', $style));
        }

        $this->getHTMLPage()->find($this->getLocator('styleDropdown'))->click();

        $blockStyleLocator = new VisibleCSSLocator('blockStyle', sprintf($this->getLocator('blockStyle')->getSelector(), $style));
        $this->getHTMLPage()->find($blockStyleLocator)->click();
    }

    public function insertNewLine(): void
    {
        $this->getFieldInput();
        $this->getSession()->getDriver()->executeScript(
            sprintf($this->executeAlloyEditorScript, $this->richtextId, 'enter')
        );
    }

    public function insertLine($value, $style = ''): void
    {
        $this->getFieldInput();
        $this->getSession()->getDriver()->executeScript(
            sprintf($this->insertAlloyEditorValueScript, $this->richtextId, $value)
        );

        if ($style === '') {
            return;
        }

        $this->changeStyle($style);

        $selector = CSSLocatorBuilder::base($this->getLocator('fieldInput'))->withDescendant(new VisibleCSSLocator('style', $style))->build();

        Assert::assertStringContainsString(
            sprintf('%s%s</%s>', $value, '<br>', $style),
            $this->getHTMLPage()->find($selector)->getOuterHtml()
        );
    }

    private function getFieldInput(): ElementInterface
    {
        $fieldInput = $this->getHTMLPage()->find($this->getLocator('fieldInput'));
        $this->richtextId = $fieldInput->getAttribute('id');

        return $fieldInput;
    }

    public function addUnorderedList(array $listElements): void
    {
        $this->getFieldInput();
        $this->openElementsToolbar();
        $this->getHTMLPage()->find($this->getLocator('unorderedListButton'))->click();

        foreach ($listElements as $listElement) {
            $this->insertLine($listElement);

            if ($listElement !== end($listElements)) {
                $this->insertNewLine();
            }
        }

        $actualListElements = $this->getHTMLPage()->findAll($this->getLocator('unorderedListElement'));
        $listElementsText = [];
        foreach ($actualListElements as $actualListElement) {
            $listElementsText[] = $actualListElement->getText();
        }

        Assert::assertEquals($listElements, $listElementsText);
    }

    public function clickEmbedInlineButton(): void
    {
        $this->getHTMLPage()->find($this->getLocator('embedInlineButton'))->click();
    }

    public function clickEmbedButton(): void
    {
        $this->getHTMLPage()->find($this->getLocator('embedButton'))->click();
    }

    public function equalsEmbedInlineItem($itemName): bool
    {
        return $itemName === $this->getHTMLPage()->find($this->getLocator('embedInlineTitle'))->getText();
    }

    public function equalsEmbedItem($itemName): bool
    {
        return $itemName === $this->getHTMLPage()->find($this->getLocator('embedTitle'))->getText();
    }

    public function moveElement($direction): void
    {
        if (!in_array($direction, self::ALLOWED_MOVE_OPTIONS)) {
            throw new Exception(sprintf('Unsupported direction: %s', $direction));
        }

        $moveLocator = new VisibleCSSLocator('moveButton', sprintf($this->getLocator('moveButton')->getSelector(), $direction));
        $this->getHTMLPage()->find($moveLocator)->click();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', '.ez-data-source__richtext'),
            new VisibleCSSLocator('textarea', 'textarea'),
            new VisibleCSSLocator('embedInlineButton', '.ez-btn-ae--embed-inline'),
            new VisibleCSSLocator('embedButton', '.ez-btn-ae--embed'),
            new VisibleCSSLocator('addButton', '.ae-button-add'),
            new VisibleCSSLocator('embedTitle', '.cke_widget_ezembed .ez-embed-content__title'),
            new VisibleCSSLocator('embedInlineTitle', '.cke_widget_ezembedinline .ez-embed-content__title'),
            new VisibleCSSLocator('unorderedListButton', '.ez-btn-ae--unordered-list'),
            new VisibleCSSLocator('unorderedListElement', '.ez-data-source__richtext ul li'),
            new VisibleCSSLocator('styleDropdown', '.ae-toolbar-element'),
            new VisibleCSSLocator('blockStyle', '.ae-listbox li %s'),
            new VisibleCSSLocator('moveButton', '.ez-btn-ae--move-%s'),
            new VisibleCSSLocator('toolbarButton', '.ae-toolbar .ez-btn-ae'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezrichtext';
    }
}
