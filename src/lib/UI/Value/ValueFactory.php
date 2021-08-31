<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\DraftList\Item\ContentDraftListItem;
use eZ\Publish\API\Repository\Values\Content\DraftList\Item\UnauthorizedContentDraftListItem;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Relation;
use eZ\Publish\API\Repository\Values\Content\RelationList\Item\RelationListItem;
use eZ\Publish\API\Repository\Values\Content\RelationList\Item\UnauthorizedRelationListItem;
use eZ\Publish\API\Repository\Values\Content\URLAlias;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\API\Repository\Values\User\Policy;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\Repository\LocationResolver\LocationResolver;
use eZ\Publish\SPI\Limitation\Target;
use eZ\Publish\SPI\Limitation\Target\Builder\VersionBuilder;
use EzSystems\EzPlatformAdminUi\Specification\UserExists;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;

class ValueFactory
{
    /** @var \eZ\Publish\API\Repository\UserService */
    protected $userService;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \eZ\Publish\API\Repository\ObjectStateService */
    protected $objectStateService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\PathService */
    protected $pathService;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \eZ\Publish\Core\Repository\LocationResolver\LocationResolver */
    protected $locationResolver;

    /**
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \eZ\Publish\API\Repository\ObjectStateService $objectStateService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\UI\Service\PathService $pathService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     * @param \eZ\Publish\Core\Repository\LocationResolver\LocationResolver $locationResolver
     */
    public function __construct(
        UserService $userService,
        LanguageService $languageService,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        SearchService $searchService,
        ObjectStateService $objectStateService,
        PermissionResolver $permissionResolver,
        PathService $pathService,
        DatasetFactory $datasetFactory,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        LocationResolver $locationResolver
    ) {
        $this->userService = $userService;
        $this->languageService = $languageService;
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
        $this->objectStateService = $objectStateService;
        $this->permissionResolver = $permissionResolver;
        $this->pathService = $pathService;
        $this->datasetFactory = $datasetFactory;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
        $this->locationResolver = $locationResolver;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     *
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function createVersionInfo(VersionInfo $versionInfo): UIValue\Content\VersionInfo
    {
        $translationsDataset = $this->datasetFactory->translations();
        $translationsDataset->load($versionInfo);

        $author = (new UserExists($this->userService))->isSatisfiedBy($versionInfo->creatorId)
            ? $this->userService->loadUser($versionInfo->creatorId) : null;

        return new UIValue\Content\VersionInfo($versionInfo, [
            'author' => $author,
            'translations' => $translationsDataset->getTranslations(),
            'userCanRemove' => $this->permissionResolver->canUser(
                'content', 'versionremove', $versionInfo
            ),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function createLanguage(Language $language, VersionInfo $versionInfo): UIValue\Content\Language
    {
        $target = (new VersionBuilder())->translateToAnyLanguageOf([$language->languageCode])->build();

        return new UIValue\Content\Language($language, [
            'userCanRemove' => $this->permissionResolver->canUser('content', 'remove', $versionInfo, [$target]),
            'userCanEdit' => $this->permissionResolver->canUser('content', 'edit', $versionInfo),
            'main' => $language->languageCode === $versionInfo->getContentInfo()->mainLanguageCode,
        ]);
    }

    /**
     * @deprecated since version 2.5, to be removed in 3.0. Please use ValueFactory::createRelationItem instead.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Relation $relation
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Relation
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\ForbiddenException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function createRelation(Relation $relation, Content $content): UIValue\Content\Relation
    {
        $contentType = $content->getContentType();

        return new UIValue\Content\Relation($relation, [
            'relationFieldDefinitionName' => $this->getRelationFieldDefinitionName($relation, $contentType),
            'relationContentTypeName' => $contentType->getName(),
            'relationLocation' => $this->locationResolver->resolveLocation($content->contentInfo),
            'relationName' => $content->getName(),
            'resolvedSourceLocation' => $this->locationResolver->resolveLocation($relation->sourceContentInfo),
            'resolvedDestinationLocation' => $this->locationResolver->resolveLocation($relation->destinationContentInfo),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\RelationList\Item\RelationListItem $relationListItem
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Relation
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\ForbiddenException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function createRelationItem(RelationListItem $relationListItem, Content $content): UIValue\Content\Relation
    {
        $contentType = $content->getContentType();
        $relation = $relationListItem->getRelation();

        return new UIValue\Content\Relation($relation, [
            'relationFieldDefinitionName' => $this->getRelationFieldDefinitionName($relation, $contentType),
            'relationContentTypeName' => $contentType->getName(),
            'relationLocation' => $this->locationResolver->resolveLocation($content->contentInfo),
            'relationName' => $content->getName(),
            'resolvedSourceLocation' => $this->locationResolver->resolveLocation($relation->sourceContentInfo),
            'resolvedDestinationLocation' => $this->locationResolver->resolveLocation($relation->destinationContentInfo),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\RelationList\Item\UnauthorizedRelationListItem $relationListItem
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\RelationInterface
     */
    public function createUnauthorizedRelationItem(
        UnauthorizedRelationListItem $relationListItem
    ): UIValue\Content\RelationInterface {
        return new UIValue\Content\UnauthorizedRelation($relationListItem);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function createLocation(Location $location): UIValue\Content\Location
    {
        $translations = $location->getContent()->getVersionInfo()->languageCodes;
        $target = (new Target\Version())->deleteTranslations($translations);

        return new UIValue\Content\Location($location, [
            'childCount' => $this->locationService->getLocationChildCount($location),
            'pathLocations' => $this->pathService->loadPathLocations($location),
            'userCanManage' => $this->permissionResolver->canUser(
                'content', 'manage_locations', $location->getContentInfo()
            ),
            'userCanRemove' => $this->permissionResolver->canUser(
                'content', 'remove', $location->getContentInfo(), [$location, $target]
            ),
            'userCanEdit' => $this->permissionResolver->canUser(
                'content', 'edit', $location->getContentInfo(), [$location]
            ),
            'main' => $location->getContentInfo()->mainLocationId === $location->id,
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup
     *
     * @return UIValue\ObjectState\ObjectState
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function createObjectState(
        ContentInfo $contentInfo,
        ObjectStateGroup $objectStateGroup
    ): UIValue\ObjectState\ObjectState {
        $objectState = $this->objectStateService->getContentState($contentInfo, $objectStateGroup);

        return new UIValue\ObjectState\ObjectState($objectState, [
            'userCanAssign' => $this->permissionResolver->canUser('state', 'assign', $contentInfo, [$objectState]),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\URLAlias $urlAlias
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\UrlAlias
     */
    public function createUrlAlias(URLAlias $urlAlias): UIValue\Content\UrlAlias
    {
        return new UIValue\Content\UrlAlias($urlAlias);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\RoleAssignment $roleAssignment
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\User\Role
     */
    public function createRole(RoleAssignment $roleAssignment): UIValue\User\Role
    {
        return new UIValue\User\Role($roleAssignment);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Policy $policy
     * @param \eZ\Publish\API\Repository\Values\User\RoleAssignment $roleAssignment
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\User\Policy
     */
    public function createPolicy(Policy $policy, RoleAssignment $roleAssignment): UIValue\User\Policy
    {
        return new UIValue\User\Policy($policy, ['role_assignment' => $roleAssignment]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Location\Bookmark
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function createBookmark(Location $location): UIValue\Location\Bookmark
    {
        return new UIValue\Location\Bookmark(
            $location,
            [
                'contentType' => $this->contentTypeService->loadContentType(
                    $location->getContentInfo()->contentTypeId,
                    $this->userLanguagePreferenceProvider->getPreferredLanguages()
                ),
                'pathLocations' => $this->pathService->loadPathLocations($location),
                'userCanEdit' => $this->permissionResolver->canUser('content', 'edit', $location->contentInfo),
            ]
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\Language
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function createLanguageFromContentType(
        Language $language,
        ContentType $contentType
    ): UIValue\Content\Language {
        return new UIValue\Content\Language($language, [
            'userCanRemove' => $this->permissionResolver->canUser('class', 'update', $contentType),
            'main' => $language->languageCode === $contentType->mainLanguageCode,
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\DraftList\Item\ContentDraftListItem $contentDraftListItem
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraftInterface
     */
    public function createContentDraft(
        ContentDraftListItem $contentDraftListItem,
        ContentType $contentType
    ): UIValue\Content\ContentDraftInterface {
        $versionInfo = $contentDraftListItem->getVersionInfo();
        $contentInfo = $versionInfo->contentInfo;
        $versionId = new UIValue\Content\VersionId(
            $contentInfo->id,
            $versionInfo->versionNo
        );

        return new UIValue\Content\ContentDraft(
            $versionInfo,
            $versionId,
            $contentType
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\DraftList\Item\UnauthorizedContentDraftListItem $contentDraftListItem
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraftInterface
     */
    public function createUnauthorizedContentDraft(
        UnauthorizedContentDraftListItem $contentDraftListItem
    ): UIValue\Content\ContentDraftInterface {
        return new UIValue\Content\UnauthorizedContentDraft($contentDraftListItem);
    }

    private function getRelationFieldDefinitionName(?Relation $relation, ContentType $contentType): string
    {
        if ($relation !== null && $relation->sourceFieldDefinitionIdentifier !== null) {
            $fieldDefinition = $contentType->getFieldDefinition(
                $relation->sourceFieldDefinitionIdentifier
            );

            if ($fieldDefinition !== null) {
                return $fieldDefinition->getName();
            }
        }

        return '';
    }
}
