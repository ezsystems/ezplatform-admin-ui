<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Relation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;

class ValueFactory
{
    /** @var UserService */
    protected $userService;

    /** @var LanguageService */
    protected $languageService;

    /** @var LocationService */
    protected $locationService;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var SearchService */
    protected $searchService;

    /** @var PermissionResolver */
    protected $permissionResolver;

    /** @var DatasetFactory */
    protected $datasetFactory;

    /** @var PathService */
    protected $pathService;

    /**
     * @param UserService $userService
     * @param LanguageService $languageService
     * @param LocationService $locationService
     * @param ContentTypeService $contentTypeService
     * @param SearchService $searchService
     * @param PermissionResolver $permissionResolver
     * @param PathService $pathService
     * @param DatasetFactory $datasetFactory
     */
    public function __construct(
        UserService $userService,
        LanguageService $languageService,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        PermissionResolver $permissionResolver,
        PathService $pathService,
        DatasetFactory $datasetFactory
    ) {
        $this->userService = $userService;
        $this->languageService = $languageService;
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
        $this->permissionResolver = $permissionResolver;
        $this->pathService = $pathService;
        $this->datasetFactory = $datasetFactory;
    }

    /**
     * @param VersionInfo $versionInfo
     *
     * @return UIValue\Content\VersionInfo
     */
    public function createVersionInfo(VersionInfo $versionInfo): UIValue\Content\VersionInfo
    {
        $translationsDataset = $this->datasetFactory->translations();
        $translationsDataset->load($versionInfo);

        return new UIValue\Content\VersionInfo($versionInfo, [
            'author' => $this->userService->loadUser($versionInfo->creatorId),
            'translations' => $translationsDataset->getTranslations(),
        ]);
    }

    /**
     * @param Language $language
     * @param VersionInfo $versionInfo
     *
     * @return UIValue\Content\Language
     */
    public function createLanguage(Language $language, VersionInfo $versionInfo): UIValue\Content\Language
    {
        return new UIValue\Content\Language($language, [
            'userCanRemove' => $this->permissionResolver->canUser('content', 'delete', $versionInfo),
            'main' => $language->languageCode === $versionInfo->getContentInfo()->mainLanguageCode,
        ]);
    }

    /**
     * @param Relation $relation
     * @param ContentInfo $contentInfo
     *
     * @return UIValue\Content\Relation
     */
    public function createRelation(Relation $relation, ContentInfo $contentInfo): UIValue\Content\Relation
    {
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
        $fieldDefinition = $contentType->getFieldDefinition($relation->sourceFieldDefinitionIdentifier);

        return new UIValue\Content\Relation($relation, [
            'relationFieldDefinitionName' => $fieldDefinition ? $fieldDefinition->getName() : '',
            'relationContentTypeName' => $contentType->getName(),
            'relationLocation' => $this->locationService->loadLocation($contentInfo->mainLocationId),
            'relationName' => $contentInfo->name,
        ]);
    }

    /**
     * @param Location $location
     *
     * @return UIValue\Content\Location
     */
    public function createLocation(Location $location): UIValue\Content\Location
    {
        return new UIValue\Content\Location($location, [
            'childCount' => $this->locationService->getLocationChildCount($location),
            'pathLocations' => $this->pathService->loadPathLocations($location),
            'userCanManage' => $this->permissionResolver->canUser(
                'content', 'manage_locations', $location->getContentInfo()
            ),
            'userCanRemove' => $this->permissionResolver->canUser(
                'content', 'remove', $location->getContentInfo(), [$location]
            ),
            'main' => $location->getContentInfo()->mainLocationId === $location->id,
        ]);
    }
}
