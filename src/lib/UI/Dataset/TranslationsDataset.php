<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzPlatformAdminUi\UI\Value as UIValue;
use EzPlatformAdminUi\UI\Value\ValueFactory;

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
