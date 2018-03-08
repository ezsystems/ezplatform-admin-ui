<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\URLAliasService;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\UrlsTab;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UrlAliasController extends Controller
{
    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var URLAliasService */
    private $urlAliasService;

    /**
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param URLAliasService $urlAliasService
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
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->formFactory->addCustomUrl();
        $form->handleRequest($request);

        /** @var CustomUrlAddData $data */
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

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $data->getLocation()->id,
                    '_fragment' => UrlsTab::URI_FRAGMENT,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $redirectionUrl = null !== $location
            ? $this->generateUrl('_ezpublishLocation', [
                'locationId' => $location->id,
                '_fragment' => UrlsTab::URI_FRAGMENT,
            ])
            : $this->generateUrl('ezplatform.dashboard');

        return $this->redirect($redirectionUrl);
    }
}
