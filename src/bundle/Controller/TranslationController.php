<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Helper\TranslationHelper;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\TranslationsTab;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslationController extends Controller
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
     */
    public function addAction(Request $request): Response
    {
        $form = $this->formFactory->addTranslation();
        $form->handleRequest($request);

        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData $data */
        $data = $form->getData();
        $location = $data->getLocation();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationAddData $data) {
                $location = $data->getLocation();
                $contentInfo = $location->getContentInfo();
                $language = $data->getLanguage();
                $baseLanguage = $data->getBaseLanguage();

                return new RedirectResponse($this->generateUrl('ibexa.content.translate_with_location.proxy', [
                    'contentId' => $contentInfo->id,
                    'fromLanguageCode' => null !== $baseLanguage ? $baseLanguage->languageCode : null,
                    'toLanguageCode' => $language->languageCode,
                    'locationId' => $location->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $redirectionUrl = null !== $location
            ? $this->generateUrl('_ez_content_view', [
                'contentId' => $location->contentId,
                'locationId' => $location->id,
            ])
            : $this->generateUrl('ezplatform.dashboard');

        return $this->redirect($redirectionUrl);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request): Response
    {
        $form = $this->formFactory->deleteTranslation();
        $form->handleRequest($request);

        /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
        $contentInfo = $form->getData()->getContentInfo();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationDeleteData $data) {
                $contentInfo = $data->getContentInfo();

                foreach ($data->getLanguageCodes() as $languageCode => $selected) {
                    $this->contentService->deleteTranslation($contentInfo, $languageCode);

                    $this->notificationHandler->success(
                        /** @Desc("Removed '%languageCode%' translation from '%name%'.") */
                        'translation.remove.success',
                        [
                            '%languageCode%' => $languageCode,
                            '%name%' => $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo),
                        ],
                        'translation'
                    );
                }

                return new RedirectResponse($this->generateUrl('_ez_content_view', [
                    'contentId' => $contentInfo->id,
                    'locationId' => $contentInfo->mainLocationId,
                    '_fragment' => TranslationsTab::URI_FRAGMENT,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ez_content_view', [
            'contentId' => $contentInfo->id,
            'locationId' => $contentInfo->mainLocationId,
            '_fragment' => TranslationsTab::URI_FRAGMENT,
        ]));
    }
}
