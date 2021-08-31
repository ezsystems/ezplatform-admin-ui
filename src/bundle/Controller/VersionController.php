<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Helper\TranslationHelper;
use EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\VersionsTab;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VersionController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\Core\Helper\TranslationHelper */
    private $translationHelper;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        ContentService $contentService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        TranslationHelper $translationHelper
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->translationHelper = $translationHelper;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function removeAction(Request $request): Response
    {
        $isDraftForm = null !== $request->get(
            sprintf('version-remove-%s', VersionsTab::FORM_REMOVE_DRAFT)
        );

        $formName = $isDraftForm
            ? sprintf('version-remove-%s', VersionsTab::FORM_REMOVE_DRAFT)
            : sprintf('version-remove-%s', VersionsTab::FORM_REMOVE_ARCHIVED);

        $form = $this->formFactory->removeVersion(
            new VersionRemoveData(),
            $formName
        );
        $form->handleRequest($request);

        /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
        $contentInfo = $form->getData()->getContentInfo();
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (VersionRemoveData $data) {
                $contentInfo = $data->getContentInfo();

                foreach ($data->getVersions() as $versionNo => $selected) {
                    $versionInfo = $this->contentService->loadVersionInfo($contentInfo, $versionNo);
                    $this->contentService->deleteVersion($versionInfo);
                }

                $this->notificationHandler->success(
                    /** @Desc("Removed version(s) from '%name%'.") */
                    'version.delete.success',
                    [
                        '%name%' => $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo),
                    ],
                    'version'
                );

                return new RedirectResponse($this->generateUrl('_ez_content_view', [
                    'contentId' => $contentInfo->id,
                    'locationId' => $contentInfo->mainLocationId,
                    '_fragment' => VersionsTab::URI_FRAGMENT,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ez_content_view', [
            'contentId' => $contentInfo->id,
            'locationId' => $contentInfo->mainLocationId,
            '_fragment' => VersionsTab::URI_FRAGMENT,
        ]));
    }
}
