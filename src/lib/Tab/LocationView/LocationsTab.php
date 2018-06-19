<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateVisibilityData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;
use eZ\Publish\API\Repository\PermissionResolver;

class LocationsTab extends AbstractTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-locations';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        UrlGeneratorInterface $urlGenerator,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($twig, $translator);

        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->permissionResolver = $permissionResolver;
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
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function renderView(array $parameters): string
    {
        /** @var Content $content */
        $content = $parameters['content'];
        /** @var Location $location */
        $location = $parameters['location'];
        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $locations = [];

        if ($contentInfo->published) {
            $locationsDataset = $this->datasetFactory->locations();
            $locationsDataset->load($contentInfo);

            $locations = $locationsDataset->getLocations();
        }

        $formLocationAdd = $this->createLocationAddForm($location);
        $formLocationRemove = $this->createLocationRemoveForm($location, $locations);
        $formLocationSwap = $this->createLocationSwapForm($location);
        $formLocationUpdateVisibility = $this->createLocationUpdateVisibilityForm($location);
        $formLocationMainUpdate = $this->createLocationUpdateMainForm($contentInfo, $location);
        $canManageLocations = $this->permissionResolver->canUser(
            'content', 'manage_locations', $location->getContentInfo()
        );
        $canCreate = $this->permissionResolver->canUser(
            'content', 'create', $location->getContentInfo()
        );
        $canEdit = $this->permissionResolver->canUser(
            'content', 'edit', $location->getContentInfo()
        );

        $viewParameters = [
            'locations' => $locations,
            'form_content_location_add' => $formLocationAdd->createView(),
            'form_content_location_remove' => $formLocationRemove->createView(),
            'form_content_location_swap' => $formLocationSwap->createView(),
            'form_content_location_update_visibility' => $formLocationUpdateVisibility->createView(),
            'form_content_location_main_update' => $formLocationMainUpdate->createView(),
            'can_swap' => $canEdit,
            'can_add' => $canManageLocations && $canCreate,
        ];

        return $this->twig->render(
            '@ezdesign/content/tab/locations/tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }

    /**
     * @param Location $location
     *
     * @return FormInterface
     */
    private function createLocationAddForm(Location $location): FormInterface
    {
        return $this->formFactory->addLocation(
            new ContentLocationAddData($location->getContentInfo())
        );
    }

    /**
     * @param Location $location
     * @param UIValue\Content\Location[] $contentLocations
     *
     * @return FormInterface
     */
    private function createLocationRemoveForm(Location $location, array $contentLocations): FormInterface
    {
        return $this->formFactory->removeLocation(
            new ContentLocationRemoveData($location->getContentInfo(), $this->getLocationChoices($contentLocations))
        );
    }

    /**
     * @param UIValue\Content\Location[] $locations
     *
     * @return array
     */
    private function getLocationChoices(array $locations): array
    {
        $locationIds = array_column($locations, 'id');

        return array_combine($locationIds, array_fill_keys($locationIds, false));
    }

    /**
     * @param Location $location
     *
     * @return FormInterface
     */
    protected function createLocationSwapForm(Location $location): FormInterface
    {
        return $this->formFactory->swapLocation(
            new LocationSwapData($location)
        );
    }

    /**
     * @param Location $location
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    protected function createLocationUpdateVisibilityForm(Location $location): FormInterface
    {
        return $this->formFactory->updateVisibilityLocation(
            new LocationUpdateVisibilityData($location)
        );
    }

    /**
     * @param $contentInfo
     * @param Location $location
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    protected function createLocationUpdateMainForm($contentInfo, Location $location): FormInterface
    {
        return $this->formFactory->updateContentMainLocation(
            new ContentMainLocationUpdateData($contentInfo, $location)
        );
    }
}
