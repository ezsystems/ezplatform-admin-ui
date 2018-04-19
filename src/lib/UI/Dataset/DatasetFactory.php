<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class DatasetFactory
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /** @var \eZ\Publish\API\Repository\ObjectStateService */
    protected $objectStateService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    protected $valueFactory;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var array */
    private $userContentTypeIdentifier;

    /** @var array */
    private $userGroupContentTypeIdentifier;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ObjectStateService $objectStateService
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     * @param \eZ\Publish\API\Repository\RoleService $roleService
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     * @param array $userContentTypeIdentifier
     * @param array $userGroupContentTypeIdentifier
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LanguageService $languageService,
        LocationService $locationService,
        ObjectStateService $objectStateService,
        URLAliasService $urlAliasService,
        RoleService $roleService,
        UserService $userService,
        ValueFactory $valueFactory,
        array $userContentTypeIdentifier,
        array $userGroupContentTypeIdentifier
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->languageService = $languageService;
        $this->locationService = $locationService;
        $this->objectStateService = $objectStateService;
        $this->urlAliasService = $urlAliasService;
        $this->roleService = $roleService;
        $this->userService = $userService;
        $this->valueFactory = $valueFactory;
        $this->userContentTypeIdentifier = $userContentTypeIdentifier;
        $this->userGroupContentTypeIdentifier = $userGroupContentTypeIdentifier;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\VersionsDataset
     */
    public function versions(): VersionsDataset
    {
        return new VersionsDataset($this->contentService, $this->valueFactory);
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\TranslationsDataset
     */
    public function translations(): TranslationsDataset
    {
        return new TranslationsDataset($this->languageService, $this->valueFactory);
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\RelationsDataset
     */
    public function relations(): RelationsDataset
    {
        return new RelationsDataset($this->contentService, $this->valueFactory);
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\LocationsDataset
     */
    public function locations(): LocationsDataset
    {
        return new LocationsDataset($this->locationService, $this->valueFactory);
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\ObjectStatesDataset
     */
    public function objectStates(): ObjectStatesDataset
    {
        return new ObjectStatesDataset($this->objectStateService, $this->valueFactory);
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\CustomUrlsDataset
     */
    public function customUrls(): CustomUrlsDataset
    {
        return new CustomUrlsDataset($this->urlAliasService, $this->valueFactory);
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\RolesDataset
     */
    public function roles(): RolesDataset
    {
        return new RolesDataset(
            $this->roleService,
            $this->contentService,
            $this->contentTypeService,
            $this->userService,
            $this->valueFactory,
            $this->userContentTypeIdentifier,
            $this->userGroupContentTypeIdentifier
        );
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\PoliciesDataset
     */
    public function policies(): PoliciesDataset
    {
        return new PoliciesDataset(
            $this->roleService,
            $this->contentService,
            $this->contentTypeService,
            $this->userService,
            $this->valueFactory,
            $this->userContentTypeIdentifier,
            $this->userGroupContentTypeIdentifier
        );
    }
}
