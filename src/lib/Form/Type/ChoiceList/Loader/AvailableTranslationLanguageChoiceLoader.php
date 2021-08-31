<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;

class AvailableTranslationLanguageChoiceLoader extends BaseChoiceLoader
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /** @var string[] */
    protected $languageCodes;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param string[] $languageCodes
     */
    public function __construct(LanguageService $languageService, $languageCodes)
    {
        $this->languageService = $languageService;
        $this->languageCodes = $languageCodes;
    }

    /**
     * @inheritdoc
     */
    public function getChoiceList(): array
    {
        return array_filter(
            $this->languageService->loadLanguages(),
            function (Language $language) {
                return $language->enabled && !in_array($language->languageCode, $this->languageCodes, true);
            }
        );
    }
}
