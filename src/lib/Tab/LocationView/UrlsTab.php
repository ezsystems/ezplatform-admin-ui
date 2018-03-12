<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class UrlsTab extends AbstractTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-urls';

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    protected $urlAliasService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        URLAliasService $urlAliasService,
        FormFactory $formFactory,
        DatasetFactory $datasetFactory,
        LocationService $locationService
    ) {
        parent::__construct($twig, $translator);

        $this->urlAliasService = $urlAliasService;
        $this->formFactory = $formFactory;
        $this->datasetFactory = $datasetFactory;
        $this->locationService = $locationService;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'urls';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        /** @Desc("URL") */
        return $this->translator->trans('tab.name.urls', [], 'locationview');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 700;
    }

    /**
     * @param array $parameters
     *
     * @return string
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function renderView(array $parameters): string
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $parameters['location'];

        $translationsDataset = $this->datasetFactory->customUrls();
        $translationsDataset->load($location);
        $customUrlAliases = $translationsDataset->getCustomUrlAliases();
        $customUrlAddForm = $this->createCustomUrlAddForm($location);
        $customUrlRemoveForm = $this->createCustomUrlRemoveForm($location, $customUrlAliases);
        $parentLocation = $this->locationService->loadLocation($location->parentLocationId);

        $viewParameters = [
            'custom_urls' => $this->urlAliasService->listLocationAliases($location, true, null, true),
            'system_urls' => $this->urlAliasService->listLocationAliases($location, false),
            'form_custom_url_add' => $customUrlAddForm->createView(),
            'form_custom_url_remove' => $customUrlRemoveForm->createView(),
            'parent_name' => $parentLocation->contentInfo->name,
        ];

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab:urls.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createCustomUrlAddForm(Location $location): FormInterface
    {
        $customUrlAddData = new CustomUrlAddData($location);

        return $this->formFactory->addCustomUrl($customUrlAddData);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param array $customUrlAliases
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createCustomUrlRemoveForm(Location $location, array $customUrlAliases): FormInterface
    {
        $customUrlRemoveData = new CustomUrlRemoveData($location, $this->getChoices($customUrlAliases));

        return $this->formFactory->removeCustomUrl($customUrlRemoveData);
    }

    /**
     * @param array $customUrlAliases
     *
     * @return array
     */
    private function getChoices(array $customUrlAliases): array
    {
        $urlAliasIdList = array_column($customUrlAliases, 'id');

        return array_combine($urlAliasIdList, array_fill_keys($urlAliasIdList, false));
    }
}
