<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Language;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\TranslationsTab;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    private $contentService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->formFactory->addTranslation();
        $form->handleRequest($request);

        /** @var TranslationAddData $data */
        $data = $form->getData();
        $location = $data->getLocation();

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (TranslationAddData $data) {
                $location = $data->getLocation();
                $contentInfo = $location->getContentInfo();
                $language = $data->getLanguage();
                $baseLanguage = $data->getBaseLanguage();

                return new RedirectResponse($this->generateUrl('ezplatform.content.translate', [
                    'contentId' => $contentInfo->id,
                    'fromLanguage' => null !== $baseLanguage ? $baseLanguage->languageCode : null,
                    'toLanguage' => $language->languageCode,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $redirectionUrl = null !== $location
            ? $this->generateUrl('_ezpublishLocation', ['locationId' => $location->id])
            : $this->generateUrl('ezplatform.dashboard');

        return $this->redirect($redirectionUrl);
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

        /** @var ContentInfo $contentInfo */
        $contentInfo = $form->getData()->getContentInfo();

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationRemoveData $data) {
                $contentInfo = $data->getContentInfo();

                foreach ($data->getLanguageCodes() as $languageCode => $selected) {
                    $this->contentService->removeTranslation($contentInfo, $languageCode);

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Translation '%languageCode%' removed from content `%name%`.") */
                            'translation.remove.success',
                            ['%languageCode%' => $languageCode, '%name%' => $contentInfo->name],
                            'translation'
                        )
                    );
                }

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $contentInfo->mainLocationId,
                    '_fragment' => TranslationsTab::URI_FRAGMENT,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $contentInfo->mainLocationId,
            '_fragment' => TranslationsTab::URI_FRAGMENT,
        ]));
    }

    /**
     * @param ContentInfo $contentInfo
     * @param Language $language
     * @param Language|null $baseLanguage
     *
     * @return Content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function createTranslationDraft(
        ContentInfo $contentInfo,
        Language $language,
        ?Language $baseLanguage = null
    ): Content {
        $contentDraft = $this->contentService->createContentDraft($contentInfo);
        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = $language->languageCode;
        $contentType = $this->contentTypeService->loadContentType($contentDraft->contentInfo->contentTypeId);

        $fields = null !== $baseLanguage
            ? $contentDraft->getFieldsByLanguage($baseLanguage->languageCode)
            : $contentDraft->getFields();

        foreach ($fields as $field) {
            $fieldDef = $contentType->getFieldDefinition($field->fieldDefIdentifier);
            $value = null !== $baseLanguage
                ? $field->value
                : $fieldDef->defaultValue;
            $contentUpdateStruct->setField($field->fieldDefIdentifier, $value);
        }

        return $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct);
    }
}
