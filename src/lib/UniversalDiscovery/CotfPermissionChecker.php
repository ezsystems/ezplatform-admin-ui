<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LimitationService;
use eZ\Publish\API\Repository\Values\User\UserReference;
use eZ\Publish\Core\Limitation\ContentTypeLimitationType;
use eZ\Publish\Core\Limitation\LanguageLimitationType;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as CoreContentType;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\SPI\Limitation\Type as LimitationType;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\SPI\Limitation\Type;

final class CotfPermissionChecker
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\LimitationService */
    private $limitationService;

    /**
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\LimitationService $limitationService
     */
    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        PermissionResolver $permissionResolver,
        LimitationService $limitationService
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->permissionResolver = $permissionResolver;
        $this->limitationService = $limitationService;
    }

    /**
     * @param Location $location
     *
     * @return \EzSystems\EzPlatformAdminUi\UniversalDiscovery\CotfCreateRestrictions
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getCreateRestrictions(Location $location): CotfCreateRestrictions
    {
        $restrictedContentTypesIds = [];
        $restrictedLanguagesCodes = [];

        $contentCreateRestrictions = $this->resolvePermissionSets('content', 'create', $location);
        $contentPublishRestrictions = $this->resolvePermissionSets('content', 'publish', $location);

        $hasAccess = $contentCreateRestrictions->hasAccess && $contentPublishRestrictions->hasAccess;

        if ($hasAccess) {
            $restrictedContentTypesIds = $this->resolveRestrictions(
                $contentCreateRestrictions->restrictedContentTypesIds,
                $contentPublishRestrictions->restrictedContentTypesIds
            );

            $restrictedLanguagesCodes = $this->resolveRestrictions(
                $contentCreateRestrictions->restrictedLanguagesCodes,
                $contentPublishRestrictions->restrictedLanguagesCodes
            );
        }

        return new CotfCreateRestrictions($hasAccess, $restrictedContentTypesIds, $restrictedLanguagesCodes);
    }

    /**
     * @param string $module
     * @param string $function
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \EzSystems\EzPlatformAdminUi\UniversalDiscovery\CotfCreateRestrictions
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function resolvePermissionSets(
        string $module,
        string $function,
        Location $location
    ): CotfCreateRestrictions {
        $permissionSets = $this->permissionResolver->hasAccess($module, $function);

        if (\is_bool($permissionSets)) {
            return new CotfCreateRestrictions($permissionSets);
        }

        $restrictedContentTypesIds = [];
        $restrictedLanguagesCodes = [];
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($location->id);
        $currentUserRef = $this->permissionResolver->getCurrentUserReference();

        foreach ($permissionSets as $permissionSet) {
            if ($this->accessDeniedByRoleLimitation($permissionSet, $location, $currentUserRef)) {
                return new CotfCreateRestrictions(false);
            }

            /** @var \eZ\Publish\API\Repository\Values\User\Policy $policy */
            foreach ($permissionSet['policies'] as $policy) {
                $limitations = $policy->getLimitations();

                // Return true if policy gives full access (aka no limitations)
                if ($limitations === []) {
                    return new CotfCreateRestrictions(true);
                }

                $possibleRestrictedLanguageCodes = [];
                $possibleRestrictedContentTypes = [];

                $policyPass = true;
                foreach ($limitations as $limitation) {
                    $accessVote = null;
                    $type = $this->limitationService->getLimitationType($limitation->getIdentifier());

                    if ($type instanceof LanguageLimitationType) {
                        $possibleRestrictedLanguageCodes = $this->getPossibleLanguageCodes($limitation, $location, $type, $currentUserRef, $locationCreateStruct);
                    } elseif ($type instanceof ContentTypeLimitationType) {
                        $possibleRestrictedContentTypes = $this->getPossibleContentTypes($limitation, $location, $type, $currentUserRef, $locationCreateStruct);
                    } else {
                        $contentCreateStruct = $this->createContentCreateStruct($location, new CoreContentType(), '');
                        $accessVote = $type->evaluate($limitation, $currentUserRef, $contentCreateStruct, [$locationCreateStruct]);

                        if ($accessVote !== LimitationType::ACCESS_GRANTED) {
                            $policyPass = false;
                            break;
                        }
                    }
                }

                if ($policyPass) {
                    if (!empty($possibleRestrictedLanguageCodes)) {
                        $restrictedLanguagesCodes[] = $possibleRestrictedLanguageCodes;
                    }

                    if (!empty($possibleRestrictedContentTypes)) {
                        $restrictedContentTypesIds[] = $possibleRestrictedContentTypes;
                    }
                }
            }
        }

        return new CotfCreateRestrictions(
            true,
            array_unique(array_merge(...$restrictedContentTypesIds)),
            array_unique(array_merge(...$restrictedLanguagesCodes))
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitation
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\SPI\Limitation\Type $type
     * @param \eZ\Publish\API\Repository\Values\User\UserReference $currentUserRef
     * @param \eZ\Publish\API\Repository\Values\Content\LocationCreateStruct $locationCreateStruct
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function getPossibleLanguageCodes(
        Limitation $limitation,
        Location $location,
        Type $type,
        UserReference $currentUserRef,
        LocationCreateStruct $locationCreateStruct
    ): array {
        $languageCodes = [];
        foreach ($limitation->limitationValues as $languageLimitationValue) {
            $contentCreateStruct = $this->createContentCreateStruct($location, new CoreContentType(), $languageLimitationValue);
            $access = $type->evaluate($limitation, $currentUserRef, $contentCreateStruct, [$locationCreateStruct]);

            if ($access === LimitationType::ACCESS_GRANTED) {
                $languageCodes[] = $languageLimitationValue;
            }
        }

        return $languageCodes;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitation
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\SPI\Limitation\Type $type
     * @param \eZ\Publish\API\Repository\Values\User\UserReference $currentUserRef
     * @param \eZ\Publish\API\Repository\Values\Content\LocationCreateStruct $locationCreateStruct
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function getPossibleContentTypes(
        Limitation $limitation,
        Location $location,
        Type $type,
        UserReference $currentUserRef,
        LocationCreateStruct $locationCreateStruct
    ): array {
        $contentTypeList = $this->contentTypeService->loadContentTypeList($limitation->limitationValues);
        $possibleContentTypes = [];
        foreach ($contentTypeList as $contentType) {
            $contentCreateStruct = $this->createContentCreateStruct($location, $contentType, '');
            $access = $type->evaluate($limitation, $currentUserRef, $contentCreateStruct, [$locationCreateStruct]);

            if ($access === LimitationType::ACCESS_GRANTED) {
                $possibleContentTypes[] = $contentType->id;
            }
        }

        return $possibleContentTypes;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @param string $language
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentCreateStruct
     */
    private function createContentCreateStruct(
        Location $location,
        ContentType $contentType,
        string $language
    ): ContentCreateStruct {
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language);
        $contentCreateStruct->sectionId = $location->contentInfo->sectionId;

        return $contentCreateStruct;
    }

    /**
     * @param array $permissionSet
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\User\UserReference $currentUserRef
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function accessDeniedByRoleLimitation(array $permissionSet, Location $location, UserReference $currentUserRef): bool
    {
        if (!$permissionSet['limitation'] instanceof Limitation) {
            return false;
        }

        $type = $this->limitationService->getLimitationType($permissionSet['limitation']->getIdentifier());
        $contentCreateStruct = $this->createContentCreateStruct($location, new CoreContentType(), '');
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($location->id);

        $evaluateRoleLimitation = $type->evaluate($permissionSet['limitation'], $currentUserRef, $contentCreateStruct, [$locationCreateStruct]);

        return LimitationType::ACCESS_DENIED === $evaluateRoleLimitation;
    }

    /**
     * @param array $createRestrictions
     * @param array $publishRestrictions
     *
     * @return array
     */
    private function resolveRestrictions(array $createRestrictions, array $publishRestrictions): array
    {
        if (!empty($createRestrictions) && !empty($publishRestrictions)) {
            $restrictions = array_intersect($createRestrictions, $publishRestrictions);
        } else {
            $restrictions = array_merge($createRestrictions, $publishRestrictions);
        }

        return $restrictions;
    }
}
