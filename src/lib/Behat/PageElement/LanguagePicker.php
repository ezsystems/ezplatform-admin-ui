<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;


class LanguagePicker extends Element
{
    public function chooseLanguage($language)
    {
        $this->context->getElementByText($language, '#content_edit_language .form-check-label')->click();
    }
}