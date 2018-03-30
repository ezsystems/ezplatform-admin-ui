<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use WebDriver\Exception\ElementNotVisible;

class LanguagePicker extends Element
{
    private $loadingTimeout;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'languagePickerSelector' => '#content_edit_language',
            'languageSelector' => '#content_edit_language .form-check-label',
        ];
        $this->loadingTimeout = 5;
    }

    public function chooseLanguage($language)
    {
        $this->context->getElementByText($language, $this->fields['languageSelector'])->click();
    }

    public function isVisible()
    {
        try {
            $this->context->waitUntilElementIsVisible($this->fields['languagePickerSelector'], $this->loadingTimeout);

            return true;
        } catch (ElementNotVisible $e) {
            return false;
        }
    }
}
