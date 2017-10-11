<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class TranslationsDataset
{
    /** @var LanguageService */
    protected $languageService;

    /** @var ValueFactory */
    protected $valueFactory;

    /** @var UIValue\Content\Language[] */
    protected $data;

    /**
     * @param LanguageService $languageService
     * @param ValueFactory $valueFactory
     */
    public function __construct(LanguageService $languageService, ValueFactory $valueFactory)
    {
        $this->languageService = $languageService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param VersionInfo $versionInfo
     *
     * @return TranslationsDataset
     */
    public function load(VersionInfo $versionInfo): self
    {
        $languages = [];
        foreach ($versionInfo->languageCodes as $languageCode) {
            $languages[] = $this->languageService->loadLanguage($languageCode);
        }

        $this->data = array_map(
            function (Language $language) use ($versionInfo) {
                return $this->valueFactory->createLanguage($language, $versionInfo);
            },
            $languages
        );

        return $this;
    }

    /**
     * @return UIValue\Content\Language[]
     */
    public function getTranslations(): array
    {
        return $this->data;
    }

    /**
     * @return UIValue\Content\Language[]
     */
    public function getLanguageCodes(): array
    {
        return array_column($this->data, 'languageCode');
    }
}
