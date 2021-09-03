<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use ErrorException;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use PHPUnit\Framework\Assert;

class ContentPreviewPage extends Page
{
    protected function getRoute(): string
    {
        throw new ErrorException('Preview page cannot be opened on its own!');
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('previewNav'))->isVisible());
    }

    public function getName(): string
    {
        return 'Content preview';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('previewNav', '.ez-preview__nav'),
            new VisibleCSSLocator('backToEdit', '.ez-preview__nav .ez-preview__item--back a'),
            new VisibleCSSLocator('title', '.ez-preview__nav .ez-preview__item--description'),
            new VisibleCSSLocator('desktop', '.ez-preview__nav .ez-preview__item--actions .ibexa-icon--desktop'),
            new VisibleCSSLocator('tablet', '.ez-preview__nav .ez-preview__item--actions .ibexa-icon--tablet'),
            new VisibleCSSLocator('mobile', '.ez-preview__nav .ez-preview__item--actions .ibexa-icon--mobile'),
            new VisibleCSSLocator('selectedView', '.ez-preview__action--selected'),
        ];
    }

    public function goBackToEditView(): void
    {
        $this->getHTMLPage()->find($this->getLocator('backToEdit'))->click();
    }

    public function goToView(string $viewName): void
    {
        if ($viewName !== $this->getActiveViewName()) {
            $this->getHTMLPage()->find($this->getLocator($viewName))->click();
        }
    }

    public function getActiveViewName(): string
    {
        return $this->getHTMLPage()->find($this->getLocator('selectedView'))->getAttribute('data-preview-mode');
    }
}
