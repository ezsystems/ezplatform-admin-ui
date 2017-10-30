<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\VersionsTab;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class VersionController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentService */
    private $contentService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param ContentService $contentService
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        ContentService $contentService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function removeAction(Request $request): Response
    {
        $isDraftForm = null !== $request->get(VersionsTab::FORM_REMOVE_DRAFT);
        $formName = sprintf('version-remove-%s', $isDraftForm
            ? VersionsTab::FORM_REMOVE_DRAFT
            : VersionsTab::FORM_REMOVE_ARCHIVED
        );

        $form = $this->formFactory->removeVersion(
            new VersionRemoveData(),
            $formName
        );
        $form->handleRequest($request);

        /** @var ContentInfo $contentInfo */
        $contentInfo = $form->getData()->getContentInfo();
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (VersionRemoveData $data) {
                $contentInfo = $data->getContentInfo();

                foreach ($data->getVersions() as $versionNo => $selected) {
                    $versionInfo = $this->contentService->loadVersionInfo($contentInfo, $versionNo);
                    $this->contentService->deleteVersion($versionInfo);
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Versions removed from `%name%` content.") */
                        'version.delete.success',
                        ['%name%' => $contentInfo->name],
                        'version'
                    )
                );

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $contentInfo->mainLocationId,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $contentInfo->mainLocationId,
        ]));
    }
}
