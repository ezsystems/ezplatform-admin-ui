<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\UiFormData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentService */
    protected $contentService;

    /** @var FormFactory */
    protected $formFactory;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param ContentService $contentService
     * @param FormFactory $formFactory
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        ContentService $contentService,
        FormFactory $formFactory
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->contentService = $contentService;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function removeAction(Request $request): Response
    {
        $form = $this->formFactory->removeTranslation();
        $form->handleRequest($request);

        /** @var UiFormData $uiFormData */
        $uiFormData = $form->getData();
        /** @var TranslationRemoveData $translationRemoveData */
        $translationRemoveData = $uiFormData->getData();

        $contentInfo = $translationRemoveData->getContentInfo();

        if ($form->isValid() && $form->isSubmitted()) {
            foreach ($translationRemoveData->getLanguageCodes() as $languageCode => $selected) {
                $this->contentService->removeTranslation($contentInfo, $languageCode);
            }

            $this->flashSuccess('translation.remove.success', [
                '%languageCodes%' => implode(', ', array_keys($translationRemoveData->getLanguageCodes())),
                '%contentName%' => $contentInfo->name,
            ], 'translation');

            return $this->redirect($uiFormData->getOnSuccessRedirectionUrl());
        }

        /**
         * @todo We should implement a service for converting form errors into notifications
         */
        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirectToRoute($uiFormData->getOnFailureRedirectionUrl());
    }
}
