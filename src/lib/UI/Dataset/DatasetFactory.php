<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Dataset;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
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

    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LanguageService $languageService,
        LocationService $locationService,
        ObjectStateService $objectStateService,
        URLAliasService $urlAliasService,
        RoleService $roleService,
        UserService $userService,
        BookmarkService $bookmarkService,
        ValueFactory $valueFactory,
        ConfigResolverInterface $configResolver
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->languageService = $languageService;
        $this->locationService = $locationService;
        $this->objectStateService = $objectStateService;
        $this->urlAliasService = $urlAliasService;
        $this->roleService = $roleService;
        $this->userService = $userService;
        $this->bookmarkService = $bookmarkService;
        $this->valueFactory = $valueFactory;
        $this->configResolver = $configResolver;
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
     * @deprecated since version 2.5, to be removed in 3.0. Please use DatasetFactory::relationList and DatasetFactory::reverseRelationList instead.
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\RelationsDataset
     */
    public function relations(): RelationsDataset
    {
        return new RelationsDataset($this->contentService, $this->valueFactory);
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\RelationListDataset
     */
    public function relationList(): RelationListDataset
    {
        return new RelationListDataset(
            $this->contentService,
            $this->valueFactory
        );
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\ReverseRelationListDataset
     */
    public function reverseRelationList(): ReverseRelationListDataset
    {
        return new ReverseRelationListDataset(
            $this->contentService,
            $this->valueFactory
        );
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
            $this->configResolver->getParameter('user_content_type_identifier'),
            $this->configResolver->getParameter('user_group_content_type_identifier')
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
            $this->configResolver->getParameter('user_content_type_identifier'),
            $this->configResolver->getParameter('user_group_content_type_identifier')
        );
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\BookmarksDataset
     */
    public function bookmarks(): BookmarksDataset
    {
        return new BookmarksDataset(
            $this->bookmarkService,
            $this->valueFactory
        );
    }

    /**
     * @deprecated since version 2.5, to be removed in 3.0. Please use DatasetFactory::contentDraftList instead.
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\ContentDraftsDataset
     */
    public function contentDrafts(): ContentDraftsDataset
    {
        return new ContentDraftsDataset(
            $this->contentService,
            $this->contentTypeService,
            $this->locationService
        );
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Dataset\ContentDraftListDataset
     */
    public function contentDraftList(): ContentDraftListDataset
    {
        return new ContentDraftListDataset(
            $this->contentService,
            $this->contentTypeService,
            $this->valueFactory
        );
    }
}
