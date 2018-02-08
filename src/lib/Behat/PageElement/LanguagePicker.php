<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class LanguagePicker extends Element
{
    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'languageSelector' => '#content_edit_language .form-check-label',
        ];
    }

    public function chooseLanguage($language)
    {
        $this->context->getElementByText($language, $this->fields['languageSelector'])->click();
    }
}
