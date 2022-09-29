<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class TranslationDialog extends Dialog
{
    protected function chooseFromDropdown(string $language): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('dropdownItem'))
            ->getByCriterion(new ElementTextCriterion($language))
            ->click();
    }

    public function selectNewTranslation(string $languageName): void
    {
        $this->getHTMLPage()->find($this->getLocator('expandNewTranslationDropdown'))->click();
        $this->chooseFromDropdown($languageName);
    }

    public function selectBaseTranslation(string $languageName): void
    {
        $this->getHTMLPage()->find($this->getLocator('expandBaseTranslationDropdown'))->click();
        $this->chooseFromDropdown($languageName);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(3)
            ->find($this->getLocator('addTranslationPopupModalTitle'))
            ->assert()->textEquals('Create a new translation');
    }

    protected function specifyLocators(): array
    {
        return array_merge(parent::specifyLocators(), [
            new VisibleCSSLocator('expandNewTranslationDropdown', '.ez-custom-dropdown[data-source-selector=".ez-translation__language-wrapper--language"] .ez-custom-dropdown__selection-info'),
            new VisibleCSSLocator('expandBaseTranslationDropdown', '.ez-custom-dropdown[data-source-selector=".ez-translation__language-wrapper--base-language"] .ez-custom-dropdown__selection-info'),
            new VisibleCSSLocator('dropdownItem', '.ez-custom-dropdown__item .ez-custom-dropdown__item-label'),
            new VisibleCSSLocator('addTranslationPopupModalTitle', '#add-translation-modal .modal-title'),
        ]);
    }
}
