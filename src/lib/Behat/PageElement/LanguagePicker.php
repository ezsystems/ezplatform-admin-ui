<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Element\NodeElement;
use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;
use WebDriver\Exception\ElementNotVisible;

class LanguagePicker extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'LanguagePicker';

    private $loadingTimeout;

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'languagePickerSelector' => '#content_edit_language',
            'languageSelector' => '#content_edit_language .form-check-label',
        ];
        $this->loadingTimeout = 5;
    }

    public function chooseLanguage($language): void
    {
        $this->context->getElementByText($language, $this->fields['languageSelector'])->click();
    }

    public function getLanguages(): array
    {
        return array_map(function (NodeElement $element) {
            return $element->getText();
        }, $this->context->findAllElements($this->fields['languageSelector']));
    }

    public function isVisible(): bool
    {
        try {
            $this->context->waitUntilElementIsVisible($this->fields['languagePickerSelector'], $this->loadingTimeout);

            return true;
        } catch (ElementNotVisible $e) {
            return false;
        }
    }
}
