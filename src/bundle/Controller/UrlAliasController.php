<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\URLAliasService;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\UrlsTab;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UrlAliasController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    protected $submitHandler;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    protected $urlAliasService;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     */
    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        URLAliasService $urlAliasService
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->urlAliasService = $urlAliasService;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->formFactory->addCustomUrl();
        $form->handleRequest($request);

        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData $data */
        $data = $form->getData();
        $location = $data->getLocation();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (CustomUrlAddData $data) {
                $this->urlAliasService->createUrlAlias(
                    $data->getLocation(),
                    $data->getPath(),
                    $data->getLanguage()->languageCode,
                    $data->isRedirect()
                );

                return $this->redirectToLocation($data->getLocation(), UrlsTab::URI_FRAGMENT);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        if ($location) {
            return $this->redirectToLocation($location, UrlsTab::URI_FRAGMENT);
        }

        return $this->redirectToRoute('ezplatform.dashboard');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request): Response
    {
        $form = $this->formFactory->removeCustomUrl();
        $form->handleRequest($request);

        $location = $form->getData()->getLocation();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (CustomUrlRemoveData $data) {
                $aliasToRemoveList = [];
                foreach ($data->getUrlAliases() as $customUrlId => $selected) {
                    $aliasToRemoveList[] = $this->urlAliasService->load($customUrlId);
                }
                $this->urlAliasService->removeAliases($aliasToRemoveList);

                return $this->redirectToLocation($data->getLocation(), UrlsTab::URI_FRAGMENT);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        if ($location) {
            return $this->redirectToLocation($location, UrlsTab::URI_FRAGMENT);
        }

        return $this->redirectToRoute('ezplatform.dashboard');
    }
}
