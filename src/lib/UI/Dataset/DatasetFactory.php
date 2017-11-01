<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\ObjectStateService;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class DatasetFactory
{
    /** @var ContentService */
    protected $contentService;

    /** @var LanguageService */
    protected $languageService;

    /** @var ObjectStateService */
    protected $objectStateService;

    /** @var ValueFactory */
    protected $valueFactory;

    /** @var LocationService */
    protected $locationService;

    /**
     * @param ContentService $contentService
     * @param LanguageService $languageService
     * @param LocationService $locationService
     * @param ObjectStateService $objectStateService
     * @param ValueFactory $valueFactory
     */
    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        LocationService $locationService,
        ObjectStateService $objectStateService,
        ValueFactory $valueFactory
    ) {
        $this->contentService = $contentService;
        $this->languageService = $languageService;
        $this->locationService = $locationService;
        $this->objectStateService = $objectStateService;
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

    /**
     * @return ObjectStatesDataset
     */
    public function objectStates(): ObjectStatesDataset
    {
        return new ObjectStatesDataset($this->objectStateService, $this->valueFactory);
    }
}
