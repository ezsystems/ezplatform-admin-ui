<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class TranslationsDataset
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    protected $valueFactory;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language[] */
    protected $data;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(LanguageService $languageService, ValueFactory $valueFactory)
    {
        $this->languageService = $languageService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
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
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\TranslationsDataset
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function loadFromContentType(ContentType $contentType): self
    {
        $languages = [];
        foreach ($contentType->languageCodes as $languageCode) {
            $languages[] = $this->languageService->loadLanguage($languageCode);
        }

        $this->data = array_map(
            function (Language $language) use ($contentType) {
                return $this->valueFactory->createLanguageFromContentType($language, $contentType);
            },
            $languages
        );

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language[]
     */
    public function getTranslations(): array
    {
        return $this->data;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Language[]
     */
    public function getLanguageCodes(): array
    {
        return array_column($this->data, 'languageCode');
    }
}
