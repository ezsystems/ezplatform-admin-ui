<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\SPI\Limitation\Target;
use EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer;

class ContentEditTranslationChoiceLoader extends BaseChoiceLoader
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var string[] */
    private $languageCodes;

    /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer */
    private $lookupLimitationsTransformer;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer $lookupLimitationsTransformer
     * @param string[] $languageCodes
     */
    public function __construct(
        LanguageService $languageService,
        PermissionResolver $permissionResolver,
        ?ContentInfo $contentInfo,
        LookupLimitationsTransformer $lookupLimitationsTransformer,
        array $languageCodes,
        LocationService $locationService,
        ?Location $location
    ) {
        $this->languageService = $languageService;
        $this->permissionResolver = $permissionResolver;
        $this->contentInfo = $contentInfo;
        $this->languageCodes = $languageCodes;
        $this->lookupLimitationsTransformer = $lookupLimitationsTransformer;
        $this->locationService = $locationService;
        $this->location = $location;
    }

    /**
     * @inheritdoc
     */
    public function getChoiceList(): array
    {
        $languages = $this->languageService->loadLanguages();
        $limitationLanguageCodes = [];

        if (!empty($this->languageCodes)) {
            $languages = array_filter(
                $languages,
                function (Language $language) {
                    return \in_array($language->languageCode, $this->languageCodes, true);
                }
            );
        }

        $languagesCodes = array_column($languages, 'languageCode');
        if (null !== $this->contentInfo) {
            $lookupLimitations = $this->permissionResolver->lookupLimitations(
                'content',
                'edit',
                $this->contentInfo,
                [
                    (new Target\Builder\VersionBuilder())->translateToAnyLanguageOf($languagesCodes)->build(),
                    $this->locationService->loadLocation(
                        $this->location !== null
                            ? $this->location->id
                            : $this->contentInfo->mainLocationId
                    ),
                ],
                [Limitation::LANGUAGE]
            );

            $limitationLanguageCodes = $this->lookupLimitationsTransformer->getFlattenedLimitationsValues($lookupLimitations);
        }

        if (!empty($limitationLanguageCodes)) {
            $languages = array_filter(
                $languages,
                static function (Language $language) use ($limitationLanguageCodes) {
                    return \in_array($language->languageCode, $limitationLanguageCodes, true);
                }
            );
        }

        return $languages;
    }
}
