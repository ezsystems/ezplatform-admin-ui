<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\TranslationHelper;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsRoot;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class UrlsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ibexa-tab-location-view-urls';

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    protected $urlAliasService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \eZ\Publish\Core\Helper\TranslationHelper */
    private $translationHelper;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        URLAliasService $urlAliasService,
        FormFactory $formFactory,
        DatasetFactory $datasetFactory,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher,
        TranslationHelper $translationHelper
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->urlAliasService = $urlAliasService;
        $this->formFactory = $formFactory;
        $this->datasetFactory = $datasetFactory;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->translationHelper = $translationHelper;
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
     * @inheritdoc
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/urls.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $contextParameters['location'];

        $customUrlsPaginationParams = $contextParameters['custom_urls_pagination_params'];
        $systemUrlsPaginationParams = $contextParameters['system_urls_pagination_params'];

        $customUrlsDataset = $this->datasetFactory->customUrls();
        $customUrlsDataset->load($location);

        $customUrlPagerfanta = new Pagerfanta(
            new ArrayAdapter($customUrlsDataset->getCustomUrlAliases())
        );

        $customUrlPagerfanta->setMaxPerPage($customUrlsPaginationParams['limit']);
        $customUrlPagerfanta->setCurrentPage(min($customUrlsPaginationParams['page'],
            $customUrlPagerfanta->getNbPages()));

        $systemUrlPagerfanta = new Pagerfanta(
            new ArrayAdapter($this->urlAliasService->listLocationAliases($location, false, null, true))
        );

        $systemUrlPagerfanta->setMaxPerPage($systemUrlsPaginationParams['limit']);
        $systemUrlPagerfanta->setCurrentPage(min($systemUrlsPaginationParams['page'],
            $systemUrlPagerfanta->getNbPages()));

        $customUrlAddForm = $this->createCustomUrlAddForm($location);
        $customUrlRemoveForm = $this->createCustomUrlRemoveForm($location,
            $customUrlPagerfanta->getCurrentPageResults());

        $canEditCustomUrl = $this->permissionResolver->hasAccess('content', 'urltranslator');

        $viewParameters = [
            'form_custom_url_add' => $customUrlAddForm->createView(),
            'form_custom_url_remove' => $customUrlRemoveForm->createView(),
            'custom_urls_pager' => $customUrlPagerfanta,
            'custom_urls_pagination_params' => $customUrlsPaginationParams,
            'system_urls_pager' => $systemUrlPagerfanta,
            'system_urls_pagination_params' => $systemUrlsPaginationParams,
            'can_edit_custom_url' => $canEditCustomUrl,
            'parent_name' => null,
        ];

        try {
            $parentLocation = $this->locationService->loadLocation($location->parentLocationId);
            if (!(new IsRoot())->isSatisfiedBy($location)) {
                $viewParameters['parent_name'] = $this->translationHelper->getTranslatedContentName(
                    $parentLocation->getContent()
                );
            }
        } catch (UnauthorizedException $exception) {
        }

        return array_replace($contextParameters, $viewParameters);
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
