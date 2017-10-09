<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class DatasetFactory
{
    /** @var ContentService */
    protected $contentService;

    /** @var LanguageService */
    protected $languageService;

    /** @var ValueFactory */
    protected $valueFactory;

    /** @var LocationService */
    protected $locationService;

    /**
     * @param ContentService $contentService
     * @param LanguageService $languageService
     * @param LocationService $locationService
     * @param ValueFactory $valueFactory
     */
    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        LocationService $locationService,
        ValueFactory $valueFactory
    ) {
        $this->contentService = $contentService;
        $this->languageService = $languageService;
        $this->locationService = $locationService;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @return VersionsDataset
     */
    public function versions(): VersionsDataset
    {
        return new VersionsDataset($this->contentService, $this->valueFactory);
    }

    /**
     * @return TranslationsDataset
     */
    public function translations(): TranslationsDataset
    {
        return new TranslationsDataset($this->languageService, $this->valueFactory);
    }

    /**
     * @return RelationsDataset
     */
    public function relations(): RelationsDataset
    {
        return new RelationsDataset($this->contentService, $this->valueFactory);
    }

    /**
     * @return LocationsDataset
     */
    public function locations(): LocationsDataset
    {
        return new LocationsDataset($this->locationService, $this->valueFactory);
    }
}
