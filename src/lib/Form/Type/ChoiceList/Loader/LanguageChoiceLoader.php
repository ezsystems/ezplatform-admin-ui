<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

class LanguageChoiceLoader extends ConfiguredLanguagesChoiceLoader
{
    /**
     * @inheritdoc
     */
    public function getChoiceList(): array
    {
        $languages = parent::getChoiceList();
        $enabledLanguages = [];

        foreach ($languages as $language) {
            if ($language->enabled) {
                $enabledLanguages[] = $language;
            }
        }

        return $enabledLanguages;
    }
}
