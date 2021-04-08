<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateVisibilityData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use eZ\Publish\API\Repository\Values\Content\Query;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;
use eZ\Publish\API\Repository\PermissionResolver;

class LocationsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-locations';
    private const PAGINATION_PARAM_NAME = 'locationstab-page';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \eZ\Publish\API\Repository\SearchService */
    protected $searchService;

    /** @var \EzSystems\EzPlatformAdminUi\Tab\LocationView\PagerLocationToDataMapper */
    protected $pagerLocationToDataMapper;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \EzSystems\EzPlatformAdminUi\Tab\LocationView\PagerLocationToDataMapper $pagerLocationToDataMapper
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        UrlGeneratorInterface $urlGenerator,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher,
        SearchService $searchService,
        RequestStack $requestStack,
        PagerLocationToDataMapper $pagerLocationToDataMapper,
        int $defaultPaginationLimit
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->permissionResolver = $permissionResolver;
        $this->requestStack = $requestStack;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
        $this->searchService = $searchService;
        $this->pagerLocationToDataMapper = $pagerLocationToDataMapper;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'locations';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        /** @Desc("Locations") */
        return $this->translator->trans('tab.name.locations', [], 'locationview');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 400;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/locations/tab.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $contextParameters['location'];
        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $locations = [];
        $pagination = null;

        if ($contentInfo->published) {
            $currentPage = $this->requestStack->getCurrentRequest()->query->getInt(
                self::PAGINATION_PARAM_NAME, 1
            );

            $locationQuery = new LocationQuery([
                'filter' => new Query\Criterion\ContentId($contentInfo->id),
            ]);

            $pagination = new Pagerfanta(
                new LocationSearchAdapter(
                    $locationQuery,
                    $this->searchService
                )
            );

            $pagination->setMaxPerPage($this->defaultPaginationLimit);
            $pagination->setCurrentPage(min(max($currentPage, 1), $pagination->getNbPages()));
            $locations = $this->pagerLocationToDataMapper->map($pagination);
        }

        $formLocationAdd = $this->createLocationAddForm($location);
        $formLocationRemove = $this->createLocationRemoveForm($location, $locations);
        $formLocationSwap = $this->createLocationSwapForm($location);
        $formLocationUpdateVisibility = $this->createLocationUpdateVisibilityForm($location);
        $formLocationMainUpdate = $this->createLocationUpdateMainForm($contentInfo, $location);
        $canManageLocations = $this->permissionResolver->canUser(
            'content', 'manage_locations', $location->getContentInfo()
        );
        // We grant access to choose a valid Location from UDW. Now it is not possible to filter locations
        // and show only those which user has access to
        $canCreate = false !== $this->permissionResolver->hasAccess('content', 'create');
        $canEdit = $this->permissionResolver->canUser(
            'content', 'edit', $location->getContentInfo()
        );
        $canHide = [];
        foreach ($locations as $location) {
            $canHide[$location->id] = $this->permissionResolver->canUser(
                'content', 'hide', $location->getContentInfo(), [$location]
            );
        }

        $viewParameters = [
            'pager' => $pagination,
            'pager_options' => [
                'pageParameter' => '[' . self::PAGINATION_PARAM_NAME . ']',
            ],
            'locations' => $locations,
            'form_content_location_add' => $formLocationAdd->createView(),
            'form_content_location_remove' => $formLocationRemove->createView(),
            'form_content_location_swap' => $formLocationSwap->createView(),
            'form_content_location_update_visibility' => $formLocationUpdateVisibility->createView(),
            'form_content_location_main_update' => $formLocationMainUpdate->createView(),
            'can_swap' => $canEdit,
            'can_add' => $canManageLocations && $canCreate,
            'can_hide' => $canHide,
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createLocationAddForm(Location $location): FormInterface
    {
        return $this->formFactory->addLocation(
            new ContentLocationAddData($location->getContentInfo())
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\Content\Location[] $contentLocations
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createLocationRemoveForm(Location $location, array $contentLocations): FormInterface
    {
        return $this->formFactory->removeLocation(
            new ContentLocationRemoveData($location->getContentInfo(), $this->getLocationChoices($contentLocations))
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\Content\Location[] $locations
     *
     * @return array
     */
    private function getLocationChoices(array $locations): array
    {
        $locationIds = array_column($locations, 'id');

        return array_combine($locationIds, array_fill_keys($locationIds, false));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createLocationSwapForm(Location $location): FormInterface
    {
        return $this->formFactory->swapLocation(
            new LocationSwapData($location)
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createLocationUpdateVisibilityForm(Location $location): FormInterface
    {
        return $this->formFactory->updateVisibilityLocation(
            new LocationUpdateVisibilityData($location)
        );
    }

    /**
     * @param $contentInfo
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createLocationUpdateMainForm($contentInfo, Location $location): FormInterface
    {
        return $this->formFactory->updateContentMainLocation(
            new ContentMainLocationUpdateData($contentInfo, $location)
        );
    }
}
