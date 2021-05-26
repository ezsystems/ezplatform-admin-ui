<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;

class PreviewNav extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'PreviewNavLink';

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'previewNav' => '.ez-preview__nav',
            'backToEdit' => '.ez-preview__nav .ez-preview__item--back a',
            'title' => '.ez-preview__nav .ez-preview__item--description',
            'desktop' => '.ez-preview__nav .ez-preview__item--actions .ibexa-icon--desktop',
            'tablet' => '.ez-preview__nav .ez-preview__item--actions .ibexa-icon--tablet',
            'mobile' => '.ez-preview__nav .ez-preview__item--actions .ibexa-icon--mobile',
            'selectedView' => '.ez-preview__action--selected',
        ];
    }

    public function verifyVisibility(): void
    {
        $this->context->findElement($this->fields['previewNav']);
    }

    public function goBackToEditView(): void
    {
        $this->context->findElement($this->fields['backToEdit'])->click();
    }

    public function goToView(string $viewName): void
    {
        if ($viewName !== $this->getActiveViewName()) {
            $this->context->findElement($this->fields[$viewName])->click();
        }
    }

    public function getActiveViewName(): string
    {
        return $this->context->findElement($this->fields['selectedView'])->getAttribute('data-preview-mode');
    }
}
