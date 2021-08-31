<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class LanguagePicker extends Component
{
    public function chooseLanguage($language): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('languageSelector'))->getByCriterion(new ElementTextCriterion($language))->click();
    }

    public function getLanguages(): array
    {
        return $this->getHTMLPage()->findAll($this->getLocator('languageSelector'))->map(
            static function (ElementInterface $element) {
                return $element->getText();
            }
        );
    }

    public function isVisible(): bool
    {
        return $this->getHTMLPage()->findAll($this->getLocator('languagePickerSelector'))->any();
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->isVisible());
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('languagePickerSelector', '.ibexa-extra-actions--edit:not(.ibexa-extra-actions--hidden) #content_edit_language'),
            new VisibleCSSLocator('languageSelector', '.ibexa-extra-actions--edit:not(.ibexa-extra-actions--hidden) #content_edit_language .form-check-label'),
        ];
    }
}
