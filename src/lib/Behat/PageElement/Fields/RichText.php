<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Context\BrowserContext;
use PHPUnit\Framework\Assert;
use Behat\Mink\Element\NodeElement;
use Exception;

class RichText extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Rich text';
    private $setAlloyEditorValueScript = 'CKEDITOR.instances.%s.setData(\'%s\')';
    private $insertAlloyEditorValueScript = 'CKEDITOR.instances.%s.insertText(\'%s\')';
    private $executeAlloyEditorScript = 'CKEDITOR.instances.%s.execCommand(\'%s\')';
    protected $richtextId;
    protected const ALLOWED_STYLE_OPTIONS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'pre'];
    protected const ALLOWED_MOVE_OPTIONS = ['up', 'down'];

    public function __construct(BrowserContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['fieldInput'] = '.ez-data-source__richtext';
        $this->fields['textarea'] = $this->fields['fieldContainer'] . ' textarea';
        $this->fields['embedInlineButton'] = '.ez-btn-ae--embed-inline';
        $this->fields['embedButton'] = '.ez-btn-ae--embed';
        $this->fields['addButton'] = '.ae-button-add';
        $this->fields['embedTitle'] = '.cke_widget_ezembed .ez-embed-content__title';
        $this->fields['embedInlineTitle'] = '.cke_widget_ezembedinline .ez-embed-content__title';
        $this->fields['unorderedListButton'] = '.ez-btn-ae--unordered-list';
        $this->fields['unorderedListElement'] = '.ez-data-source__richtext ul li';
        $this->fields['styleDropdown'] = '.ae-toolbar-element';
        $this->fields['blockStyle'] = '.ae-listbox li %s';
        $this->fields['moveButton'] = '.ez-btn-ae--move-%s';
    }

    public function setValue(array $parameters): void
    {
        $this->getFieldInput();
        $this->context->getSession()->getDriver()->executeScript(sprintf($this->setAlloyEditorValueScript, $this->richtextId, $parameters['value']));
    }

    public function getValue(): array
    {
        $fieldInput = $this->getFieldInput();

        return [$fieldInput->getText()];
    }

    public function verifyValueInItemView(array $values): void
    {
        Assert::assertEquals(
            $values['value'],
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
    }

    public function openElementsToolbar(): void
    {
        $this->context->findElement($this->fields['addButton'])->click();
    }

    public function changeStyle(string $style): void
    {
        if (!in_array($style, self::ALLOWED_STYLE_OPTIONS)) {
            throw new Exception(sprintf('Unsupported style: %s', $style));
        }

        $this->context->findElement($this->fields['styleDropdown'])->click();
        $this->context->findElement(sprintf($this->fields['blockStyle'], $style))->click();
    }

    public function insertNewLine(): void
    {
        $this->getFieldInput();
        $this->context->getSession()->getDriver()->executeScript(sprintf($this->executeAlloyEditorScript, $this->richtextId, 'enter'));
    }

    public function insertLine($value, $style = ''): void
    {
        $this->getFieldInput();
        $this->context->getSession()->getDriver()->executeScript(sprintf($this->insertAlloyEditorValueScript, $this->richtextId, $value));

        if ($style !== '') {
            $this->changeStyle($style);
            Assert::assertContains(sprintf('%s%s</%s>', $value, '<br>', $style), $this->context->findElement(sprintf('%s %s', $this->fields['fieldInput'], $style))->getOuterHtml());
        }
    }

    private function getFieldInput(): NodeElement
    {
        $fieldInput = $this->context->findElement($this->fields['fieldInput']);
        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));
        $this->richtextId = $fieldInput->getAttribute('id');

        return $fieldInput;
    }

    public function addUnorderedList(array $listElements): void
    {
        $this->getFieldInput();
        $this->openElementsToolbar();
        $this->context->findElement($this->fields['unorderedListButton'])->click();

        foreach ($listElements as $listElement) {
            $this->insertLine($listElement);

            if ($listElement !== end($listElements)) {
                $this->insertNewLine();
            }
        }

        $actualListElements = $this->context->findAllElements($this->fields['unorderedListElement']);
        $listElementsText = [];
        foreach ($actualListElements as $actualListElement) {
            $listElementsText[] = $actualListElement->getText();
        }

        Assert::assertEquals($listElements, $listElementsText);
    }

    public function clickEmbedInlineButton(): void
    {
        $this->context->findElement($this->fields['embedInlineButton'])->click();
    }

    public function clickEmbedButton(): void
    {
        $this->context->findElement($this->fields['embedButton'])->click();
    }

    public function equalsEmbedInlineItem($itemName): bool
    {
        return $itemName === $this->context->findElement($this->fields['embedInlineTitle'])->getText();
    }

    public function equalsEmbedItem($itemName): bool
    {
        return $itemName === $this->context->findElement($this->fields['embedTitle'])->getText();
    }

    public function moveElement($direction): void
    {
        if (!in_array($direction, self::ALLOWED_MOVE_OPTIONS)) {
            throw new Exception(sprintf('Unsupported direction: %s', $direction));
        }

        $this->context->findElement(sprintf($this->fields['moveButton'], $direction))->click();
    }
}
