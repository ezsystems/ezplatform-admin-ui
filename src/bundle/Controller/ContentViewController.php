<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\BookmarkService;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\ContentVisibilityUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopySubtreeData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashWithAssetData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashContainerData;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserEditData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentVisibilityUpdateType;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentHaveAssetRelation;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentHaveUniqueRelation;
use EzSystems\EzPlatformAdminUi\Specification\ContentIsUser;
use EzSystems\EzPlatformAdminUi\Specification\Location\HasChildren;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsContainer;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ContentViewParameterSupplier as SubitemsContentViewParameterSupplier;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ContentViewController extends Controller
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\PathService */
    private $pathService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ContentViewParameterSupplier */
    private $subitemsContentViewParameterSupplier;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\BookmarkService */
    private $bookmarkService;

    /** @var int */
    private $defaultDraftPaginationLimit;

    /** @var array */
    private $siteAccessLanguages;

    /** @var int */
    private $defaultRolePaginationLimit;

    /** @var int */
    private $defaultPolicyPaginationLimit;

    /** @var int */
    private $defaultSystemUrlPaginationLimit;

    /** @var int */
    private $defaultCustomUrlPaginationLimit;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $userLanguagePreferenceProvider;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $sfFormFactory;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \EzSystems\EzPlatformAdminUi\UI\Service\PathService $pathService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ContentViewParameterSupplier $subitemsContentViewParameterSupplier
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \eZ\Publish\API\Repository\BookmarkService $bookmarkService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider
     * @param int $defaultDraftPaginationLimit
     * @param array $siteAccessLanguages
     * @param int $defaultRolePaginationLimit
     * @param int $defaultPolicyPaginationLimit
     * @param int $defaultSystemUrlPaginationLimit
     * @param int $defaultCustomUrlPaginationLimit
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        LanguageService $languageService,
        PathService $pathService,
        FormFactory $formFactory,
        FormFactoryInterface $sfFormFactory,
        SubitemsContentViewParameterSupplier $subitemsContentViewParameterSupplier,
        UserService $userService,
        BookmarkService $bookmarkService,
        ContentService $contentService,
        LocationService $locationService,
        UserLanguagePreferenceProviderInterface $userLanguagePreferenceProvider,
        int $defaultDraftPaginationLimit,
        array $siteAccessLanguages,
        int $defaultRolePaginationLimit,
        int $defaultPolicyPaginationLimit,
        int $defaultSystemUrlPaginationLimit,
        int $defaultCustomUrlPaginationLimit
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->languageService = $languageService;
        $this->pathService = $pathService;
        $this->formFactory = $formFactory;
        $this->sfFormFactory = $sfFormFactory;
        $this->subitemsContentViewParameterSupplier = $subitemsContentViewParameterSupplier;
        $this->userService = $userService;
        $this->bookmarkService = $bookmarkService;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->defaultDraftPaginationLimit = $defaultDraftPaginationLimit;
        $this->siteAccessLanguages = $siteAccessLanguages;
        $this->defaultRolePaginationLimit = $defaultRolePaginationLimit;
        $this->defaultPolicyPaginationLimit = $defaultPolicyPaginationLimit;
        $this->defaultSystemUrlPaginationLimit = $defaultSystemUrlPaginationLimit;
        $this->defaultCustomUrlPaginationLimit = $defaultCustomUrlPaginationLimit;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function locationViewAction(Request $request, ContentView $view): ContentView
    {
        // We should not cache ContentView because we use forms with CSRF tokens in template
        // JIRA ref: https://jira.ez.no/browse/EZP-28190
        $view->setCacheEnabled(false);

        if (!$view->getContent()->contentInfo->isTrashed()) {
            $this->supplyPathLocations($view);
            $this->subitemsContentViewParameterSupplier->supply($view);
            $this->supplyContentActionForms($view);
        }

        $this->supplyContentType($view);

        $this->supplyDraftPagination($view, $request);
        $this->supplyCustomUrlPagination($view, $request);
        $this->supplySystemUrlPagination($view, $request);
        $this->supplyRolePagination($view, $request);
        $this->supplyPolicyPagination($view, $request);

        $this->supplyIsLocationBookmarked($view);

        $this->supplyContentReverseRelations($view);

        return $view;
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function embedViewAction(ContentView $view): ContentView
    {
        // We should not cache ContentView because we use forms with CSRF tokens in template
        // JIRA ref: https://jira.ez.no/browse/EZP-28190
        $view->setCacheEnabled(false);

        $this->supplyPathLocations($view);
        $this->supplyContentType($view);

        return $view;
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     */
    private function supplyPathLocations(ContentView $view): void
    {
        $location = $view->getLocation();
        $pathLocations = $this->pathService->loadPathLocations($location);
        $view->addParameters(['pathLocations' => $pathLocations]);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function supplyContentType(ContentView $view): void
    {
        $contentType = $this->contentTypeService->loadContentType(
            $view->getContent()->contentInfo->contentTypeId,
            $this->userLanguagePreferenceProvider->getPreferredLanguages()
        );
        $view->addParameters(['contentType' => $contentType]);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function supplyContentActionForms(ContentView $view): void
    {
        $location = $view->getLocation();
        $content = $view->getContent();
        $versionInfo = $content->getVersionInfo();

        $locationCopyType = $this->formFactory->copyLocation(
            new LocationCopyData($location)
        );

        $locationMoveType = $this->formFactory->moveLocation(
            new LocationMoveData($location)
        );

        $subitemsContentEdit = $this->formFactory->contentEdit(
            null,
            'form_subitems_content_edit'
        );

        $contentCreateType = $this->formFactory->createContent(
            $this->getContentCreateData($location)
        );

        $locationCopySubtreeType = $this->formFactory->copyLocationSubtree(
            new LocationCopySubtreeData($location)
        );

        $contentVisibilityUpdateForm = $this->sfFormFactory->create(
            ContentVisibilityUpdateType::class,
            new ContentVisibilityUpdateData($location->contentInfo, $location->getContentInfo()->isHidden)
        );

        $view->addParameters([
            'form_location_copy' => $locationCopyType->createView(),
            'form_location_move' => $locationMoveType->createView(),
            'form_content_create' => $contentCreateType->createView(),
            'form_content_visibility_update' => $contentVisibilityUpdateForm->createView(),
            'form_subitems_content_edit' => $subitemsContentEdit->createView(),
            'form_location_copy_subtree' => $locationCopySubtreeType->createView(),
        ]);

        $contentHaveAssetRelation = new ContentHaveAssetRelation($this->contentService);

        if ($contentHaveAssetRelation
            ->and(new ContentHaveUniqueRelation($this->contentService))
            ->isSatisfiedBy($content)
        ) {
            $trashWithAssetType = $this->formFactory->trashLocationWithAsset(
                new LocationTrashWithAssetData($location)
            );
            $contentEditType = $this->createContentEditForm($content->contentInfo, $versionInfo, null, $location);

            $view->addParameters([
                'form_location_trash_with_single_asset' => $trashWithAssetType->createView(),
                'form_content_edit' => $contentEditType->createView(),
            ]);
        } elseif ($contentHaveAssetRelation->isSatisfiedBy($content)) {
            $locationTrashType = $this->formFactory->trashLocation(
                new LocationTrashData($location)
            );
            $contentEditType = $this->createContentEditForm($content->contentInfo, $versionInfo, null, $location);

            $view->addParameters([
                'form_location_trash_with_asset' => $locationTrashType->createView(),
                'form_content_edit' => $contentEditType->createView(),
            ]);
        } elseif ((new ContentIsUser($this->userService))->isSatisfiedBy($content)) {
            $userDeleteType = $this->formFactory->deleteUser(
                new UserDeleteData($content->contentInfo)
            );
            $userEditType = $this->formFactory->editUser(
                new UserEditData($content->contentInfo, $versionInfo, null, $location)
            );

            $view->addParameters([
                'form_user_delete' => $userDeleteType->createView(),
                'form_user_edit' => $userEditType->createView(),
            ]);
        } else {
            $locationTrashType = $this->formFactory->trashLocation(
                new LocationTrashData($location)
            );
            $contentEditType = $this->createContentEditForm($content->contentInfo, $versionInfo, null, $location);

            $view->addParameters([
                'form_location_trash' => $locationTrashType->createView(),
                'form_content_edit' => $contentEditType->createView(),
            ]);
        }

        $isContainer = new IsContainer();
        $hasChildren = new HasChildren($this->locationService);

        if ($isContainer->and($hasChildren)->isSatisfiedBy($location)) {
            $trashLocationContainerForm = $this->formFactory->trashContainerLocation(
                new LocationTrashContainerData($location)
            );
            $view->addParameters([
                'form_location_trash_container' => $trashLocationContainerForm->createView(),
            ]);
        }
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo|null $versionInfo
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createContentEditForm(
        ?ContentInfo $contentInfo = null,
        ?VersionInfo $versionInfo = null,
        ?Language $language = null,
        ?Location $location = null
    ): FormInterface {
        return $this->formFactory->contentEdit(
            new ContentEditData($contentInfo, $versionInfo, $language, $location)
        );
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function supplyDraftPagination(ContentView $view, Request $request): void
    {
        $page = $request->query->get('page');

        $view->addParameters([
            'draft_pagination_params' => [
                'route_name' => $request->get('_route'),
                'route_params' => $request->get('_route_params'),
                'page' => $page['version_draft'] ?? 1,
                'pages_map' => $page,
                'limit' => $this->defaultDraftPaginationLimit,
            ],
        ]);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function supplyCustomUrlPagination(ContentView $view, Request $request): void
    {
        $page = $request->query->get('page');

        $view->addParameters([
            'custom_urls_pagination_params' => [
                'route_name' => $request->get('_route'),
                'route_params' => $request->get('_route_params'),
                'page' => $page['custom_url'] ?? 1,
                'limit' => $this->defaultCustomUrlPaginationLimit,
            ],
        ]);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function supplySystemUrlPagination(ContentView $view, Request $request): void
    {
        $page = $request->query->get('page');

        $view->addParameters([
            'system_urls_pagination_params' => [
                'route_name' => $request->get('_route'),
                'route_params' => $request->get('_route_params'),
                'page' => $page['system_url'] ?? 1,
                'limit' => $this->defaultSystemUrlPaginationLimit,
            ],
        ]);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function supplyRolePagination(ContentView $view, Request $request): void
    {
        $page = $request->query->get('page');

        $view->addParameters([
            'roles_pagination_params' => [
                'route_name' => $request->get('_route'),
                'route_params' => $request->get('_route_params'),
                'page' => $page['role'] ?? 1,
                'limit' => $this->defaultRolePaginationLimit,
            ],
        ]);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function supplyPolicyPagination(ContentView $view, Request $request): void
    {
        $page = $request->query->get('page');

        $view->addParameters([
            'policies_pagination_params' => [
                'route_name' => $request->get('_route'),
                'route_params' => $request->get('_route_params'),
                'page' => $page['policy'] ?? 1,
                'limit' => $this->defaultPolicyPaginationLimit,
            ],
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData
     */
    private function getContentCreateData(?Location $location): ContentCreateData
    {
        $languages = $this->languageService->loadLanguages();
        $language = 1 === \count($languages)
            ? array_shift($languages)
            : null;

        return new ContentCreateData(null, $location, $language);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     */
    private function supplyIsLocationBookmarked(ContentView $view): void
    {
        $locationIsBookmarked = $view->getLocation() ? $this->bookmarkService->isBookmarked($view->getLocation()) : false;

        $view->addParameters(['location_is_bookmarked' => $locationIsBookmarked]);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     */
    private function supplyContentReverseRelations(ContentView $view): void
    {
        $contentInfo = $view->getLocation()->getContentInfo();
        $relations = $this->contentService->loadReverseRelations($contentInfo);

        $view->addParameters(['content_has_reverse_relations' => count($relations) > 0]);
    }
}
