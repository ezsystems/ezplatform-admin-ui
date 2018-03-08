<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class UrlsTab extends AbstractTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-urls';

    /** @var URLAliasService */
    protected $URLAliasService;

    /** @var FormFactory */
    private $formFactory;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param URLAliasService $URLAliasService
     * @param FormFactory $formFactory
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        URLAliasService $URLAliasService,
        FormFactory $formFactory
    ) {
        parent::__construct($twig, $translator);

        $this->URLAliasService = $URLAliasService;
        $this->formFactory = $formFactory;
    }

    public function getIdentifier(): string
    {
        return 'urls';
    }

    public function getName(): string
    {
        /** @Desc("URL") */
        return $this->translator->trans('tab.name.urls', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 700;
    }

    public function renderView(array $parameters): string
    {
        /** @var Location $location */
        $location = $parameters['location'];

        $customUrlAddForm = $this->createCustomUrlAddForm($location);

        $viewParameters = [
            'custom_urls' => $this->URLAliasService->listLocationAliases($location, true, null, true),
            'system_urls' => $this->URLAliasService->listLocationAliases($location, false),
            'form_custom_url_add' => $customUrlAddForm->createView(),
        ];

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab:urls.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }

    private function createCustomUrlAddForm(Location $location): FormInterface
    {
        $customUrlAddData = new CustomUrlAddData($location);

        return $this->formFactory->addCustomUrl($customUrlAddData);
    }
}
