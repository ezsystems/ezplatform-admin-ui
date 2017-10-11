<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUi\UI\Value as UIValue;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class LocationsTab extends AbstractTab implements OrderedTabInterface
{
    /** @var DatasetFactory */
    protected $datasetFactory;

    /** @var FormFactory */
    protected $formFactory;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param DatasetFactory $datasetFactory
     * @param FormFactory $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($twig, $translator);

        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function getIdentifier(): string
    {
        return 'locations';
    }

    public function getName(): string
    {
        /** @Desc("Locations") */
        return $this->translator->trans('tab.name.locations', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 400;
    }

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

        $locationViewUrl = $this->urlGenerator->generate($location, ['_fragment' => 'ez-tab-location-view-locations']);
        $formLocationAdd = $this->createLocationAddForm($location, $locationViewUrl);
        $formLocationRemove = $this->createLocationRemoveForm($location, $locations, $locationViewUrl);
        $formLocationSwap = $this->createLocationSwapForm($location, $locationViewUrl);

        $viewParameters = [
            'locations' => $locations,
            'form_content_location_add' => $formLocationAdd->createView(),
            'form_content_location_remove' => $formLocationRemove->createView(),
            'form_content_location_swap' => $formLocationSwap->createView(),
        ];

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab/locations:tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }

    /**
     * @param Location $location
     * @param string $locationViewUrl
     *
     * @return FormInterface
     */
    private function createLocationAddForm(Location $location, string $locationViewUrl): FormInterface
    {
        return $this->formFactory->addLocation(
            null,
            new ContentLocationAddData($location->getContentInfo()),
            $locationViewUrl,
            $locationViewUrl
        );
    }

    /**
     * @param Location $location
     * @param UIValue\Content\Location[] $contentLocations
     * @param string $locationViewUrl
     *
     * @return FormInterface
     */
    private function createLocationRemoveForm(
        Location $location,
        array $contentLocations,
        string $locationViewUrl
    ): FormInterface {
        return $this->formFactory->removeLocation(
            null,
            new ContentLocationRemoveData($location->getContentInfo(), $this->getLocationChoices($contentLocations)),
            $locationViewUrl,
            $locationViewUrl
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
     * @param string $locationViewUrl
     *
     * @return FormInterface
     */
    protected function createLocationSwapForm(Location $location, string $locationViewUrl): FormInterface
    {
        return $this->formFactory->swapLocation(
            null,
            new LocationSwapData($location),
            $locationViewUrl,
            $locationViewUrl
        );
    }
}
