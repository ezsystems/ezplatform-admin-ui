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
            'contentTypeSelector' => '.form-check-label',
            'headerSelector' => '.ez-extra-actions--create .ez-extra-actions__header',
        ];
    }

    public function select(string $contentType): void
    {
        $this->context->getElementByText($contentType, $this->fields['contentTypeSelector'])->click();
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntil($this->defaultTimeout, function () {
            return $this->context->findElement($this->fields['headerSelector'])->getText() !== '';
        });

        Assert::assertEquals('Create content', $this->context->findElement($this->fields['headerSelector'])->getText());
    }

    public function isContentTypeVisible(string $contentTypeName): bool
    {
        return $this->context->getElementByText($contentTypeName, $this->fields['contentTypeSelector']) !== null;
    }

    public function getDisplayedContentTypes(): array
    {
        return array_map(function (NodeElement $element) {
            return $element->getText();
        }, $this->context->findAllElements($this->fields['contentTypeSelector']));
    }
}
