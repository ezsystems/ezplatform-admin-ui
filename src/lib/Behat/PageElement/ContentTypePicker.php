<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Element\NodeElement;
use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;
use PHPUnit\Framework\Assert;

class ContentTypePicker extends Element
{
    public const ELEMENT_NAME = 'ContentTypePicker';
    public $fields;

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'filterInput' => '.ez-extra-actions__section-content--content-type .ez-instant-filter__input',
            'filteredItem' => '.ez-extra-actions__section-content--content-type .ez-instant-filter__group-item:not([hidden])',
            'headerSelector' => '.ez-extra-actions--create .ez-extra-actions__header',
        ];
    }

    public function select(string $contentTypeName): void
    {
        $countBeforeFiltering = $this->getDisplayedItemsCount();
        $this->context->findElement($this->fields['filterInput'])->setValue($contentTypeName);

        $this->context->waitUntil($this->defaultTimeout, function () use ($countBeforeFiltering) {
            return $this->getDisplayedItemsCount() < $countBeforeFiltering;
        });
        $this->context->waitUntilElementIsVisible($this->fields['filteredItem']);
        $this->context->getElementByText($contentTypeName, $this->fields['filteredItem'])->click();
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntil($this->defaultTimeout, function () {
            return $this->context->findElement($this->fields['headerSelector'])->getText() !== '';
        });

        Assert::assertEquals('Create content', $this->context->findElement($this->fields['headerSelector'])->getText());

        $this->context->waitUntil($this->defaultTimeout, function () {
            $this->context->findElement($this->fields['filterInput'])->setValue('');

            return  $this->context->findElement($this->fields['filterInput'])->getValue() === '';
        });
    }

    protected function getDisplayedItemsCount(): int
    {
        return count($this->context->findAllElements($this->fields['filteredItem']));
    }

    public function isContentTypeVisible(string $contentTypeName): bool
    {
        return $this->context->getElementByText($contentTypeName, $this->fields['filteredItem']) !== null;
    }

    public function getDisplayedContentTypes(): array
    {
        return array_map(function (NodeElement $element) {
            return $element->getText();
        }, $this->context->findAllElements($this->fields['filteredItem']));
    }
}
