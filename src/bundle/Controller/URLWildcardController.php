<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\URLWildcardService;
use eZ\Publish\API\Repository\Values\Content\URLWildcard;
use eZ\Publish\API\Repository\Values\Content\URLWildcardUpdateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardData;
use EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Tab\URLManagement\URLWildcardsTab;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class URLWildcardController extends Controller
{
    /** @var \eZ\Publish\API\Repository\URLWildcardService */
    private $urlWildcardService;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    public function __construct(
        URLWildcardService $urlWildcardService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        FormFactory $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->urlWildcardService = $urlWildcardService;
        $this->notificationHandler = $notificationHandler;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->formFactory->createURLWildcard();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->submitHandler->handle($form, function (URLWildcardData $data) {
                $this->urlWildcardService->create(
                    $data->getSourceURL(),
                    $data->getDestinationUrl(),
                    (bool) $data->getForward()
                );

                $this->notificationHandler->success(
                    /** @Desc("URL Wildcard created.") */
                    'url_wildcard.create.success',
                    [],
                    'url_wildcard'
                );
            });
        }

        return $this->redirect($this->generateUrl('ezplatform.url_management', [
            '_fragment' => URLWildcardsTab::URI_FRAGMENT,
        ]));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\URLWildcard $urlWildcard
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(URLWildcard $urlWildcard, Request $request): Response
    {
        $form = $this->formFactory->createURLWildcardUpdate(
            new URLWildcardUpdateData($urlWildcard)
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form,
                function (URLWildcardUpdateData $data) use ($urlWildcard) {
                    $urlWildcardUpdateStruct = new URLWildcardUpdateStruct();
                    $urlWildcardUpdateStruct->destinationUrl = $data->getDestinationUrl();
                    $urlWildcardUpdateStruct->sourceUrl = $data->getSourceURL();
                    $urlWildcardUpdateStruct->forward = $data->getForward();

                    $this->urlWildcardService->update(
                        $urlWildcard,
                        $urlWildcardUpdateStruct
                    );

                    $this->notificationHandler->success(
                        /** @Desc("URL Wildcard updated.") */
                        'url_wildcard.update.success',
                        [],
                        'url_wildcard'
                    );

                    return $this->redirect($this->generateUrl('ezplatform.url_management', [
                        '_fragment' => URLWildcardsTab::URI_FRAGMENT,
                    ]));
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        $actionUrl = $this->generateUrl(
            'ezplatform.url_wildcard.update',
            ['urlWildcardId' => $urlWildcard->id]
        );

        return $this->render('@ezdesign/url_wildcard/update.html.twig', [
            'form' => $form->createView(),
            'actionUrl' => $actionUrl,
            'urlWildcard' => $urlWildcard,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->deleteURLWildcard();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->submitHandler->handle($form, function (URLWildcardDeleteData $data) {
                foreach ($data->getURLWildcardsChoices() as $urlWildcardId => $value) {
                    $urlWildcard = $this->urlWildcardService->load($urlWildcardId);
                    $this->urlWildcardService->remove($urlWildcard);
                }
            });

            $this->notificationHandler->success(
                /** @Desc("URL Wildcard(s) deleted.") */
                'url_wildcard.delete.success',
                [],
                'url_wildcard'
            );
        }

        return $this->redirect($this->generateUrl('ezplatform.url_management', [
            '_fragment' => URLWildcardsTab::URI_FRAGMENT,
        ]));
    }
}
