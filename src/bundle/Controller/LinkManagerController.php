<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\URLService;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\URL\URLUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\URLUsagesAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LinkManagerController extends Controller
{
    const DEFAULT_MAX_PER_PAGE = 10;

    /** @var \eZ\Publish\API\Repository\URLService */
    private $urlService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /**
     * @param \eZ\Publish\API\Repository\URLService $urlService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     */
    public function __construct(
        URLService $urlService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->urlService = $urlService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $urlId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function editAction(Request $request, int $urlId): Response
    {
        $url = $this->urlService->loadById($urlId);

        $form = $this->formFactory->createUrlEditForm(new URLUpdateData([
            'id' => $url->id,
            'url' => $url->url,
        ]));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (URLUpdateData $data) use ($url) {
                $this->urlService->updateUrl($url, $data);
                $this->notificationHandler->success(
                    /** @Desc("URL updated") */
                    'url.update.success',
                    [],
                    'linkmanager'
                );

                return $this->redirectToRoute('ezplatform.url_management');
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/link_manager/edit.html.twig', [
            'form' => $form->createView(),
            'url' => $url,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $urlId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function viewAction(Request $request, int $urlId): Response
    {
        $url = $this->urlService->loadById($urlId);

        $usages = new Pagerfanta(new URLUsagesAdapter($url, $this->urlService));
        $usages->setCurrentPage($request->query->getInt('page', 1));
        $usages->setMaxPerPage($request->query->getInt('limit', self::DEFAULT_MAX_PER_PAGE));

        $editForm = $this->formFactory->contentEdit(
            new ContentEditData()
        );

        return $this->render('@ezdesign/link_manager/view.html.twig', [
            'url' => $url,
            'can_edit' => $this->isGranted(new Attribute('url', 'update')),
            'usages' => $usages,
            'form_edit' => $editForm->createView(),
        ]);
    }
}
