<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Filter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTranslationData;
use EzSystems\EzPlatformAdminUi\Form\Data\FormMapper\ContentTranslationMapper;
use EzSystems\EzPlatformContentForms\Form\Type\Content\ContentEditType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Handles content translation form.
 */
class ContentTranslateViewFilter implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $languagePreferenceProvider;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $languagePreferenceProvider
     */
    public function __construct(
        ContentService $contentService,
        LanguageService $languageService,
        ContentTypeService $contentTypeService,
        FormFactoryInterface $formFactory,
        UserLanguagePreferenceProviderInterface $languagePreferenceProvider
    ) {
        $this->contentService = $contentService;
        $this->languageService = $languageService;
        $this->contentTypeService = $contentTypeService;
        $this->formFactory = $formFactory;
        $this->languagePreferenceProvider = $languagePreferenceProvider;
    }

    public static function getSubscribedEvents()
    {
        return [ViewEvents::FILTER_BUILDER_PARAMETERS => 'handleContentTranslateForm'];
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent $event
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function handleContentTranslateForm(FilterViewBuilderParametersEvent $event): void
    {
        $controllerAction = $event->getParameters()->get('_controller');

        if (
            'EzSystems\EzPlatformAdminUiBundle\Controller\ContentEditController::translateAction' !== $controllerAction
        ) {
            return;
        }

        $request = $event->getRequest();
        $languageCode = $request->attributes->get('toLanguageCode');
        $baseLanguageCode = $request->attributes->get('fromLanguageCode');
        $content = $this->contentService->loadContent(
            (int)$request->attributes->get('contentId'),
            null !== $baseLanguageCode ? [$baseLanguageCode] : null
        );
        $contentType = $this->contentTypeService->loadContentType(
            $content->getContentType()->id,
            $this->languagePreferenceProvider->getPreferredLanguages()
        );
        $toLanguage = $this->languageService->loadLanguage($languageCode);
        $fromLanguage = $baseLanguageCode ? $this->languageService->loadLanguage($baseLanguageCode) : null;

        $contentTranslateData = $this->resolveContentTranslationData(
            $content,
            $toLanguage,
            $fromLanguage,
            $contentType
        );
        $form = $this->resolveContentTranslateForm(
            $contentTranslateData,
            $toLanguage,
            $content
        );

        $event->getParameters()->add(['form' => $form->handleRequest($request)]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \eZ\Publish\API\Repository\Values\Content\Language $toLanguage
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $fromLanguage
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\ContentTranslationData
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    private function resolveContentTranslationData(
        Content $content,
        Language $toLanguage,
        ?Language $fromLanguage,
        ContentType $contentType
    ): ContentTranslationData {
        $contentTranslationMapper = new ContentTranslationMapper();

        return $contentTranslationMapper->mapToFormData(
            $content,
            [
                'language' => $toLanguage,
                'baseLanguage' => $fromLanguage,
                'contentType' => $contentType,
            ]
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentTranslationData $contentUpdate
     * @param \eZ\Publish\API\Repository\Values\Content\Language $toLanguage
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function resolveContentTranslateForm(
        ContentTranslationData $contentUpdate,
        Language $toLanguage,
        Content $content
    ): FormInterface {
        return $this->formFactory->create(
            ContentEditType::class,
            $contentUpdate,
            [
                'languageCode' => $toLanguage->languageCode,
                'mainLanguageCode' => $content->contentInfo->mainLanguageCode,
                'content' => $content,
                'contentUpdateStruct' => $contentUpdate,
                'drafts_enabled' => true,
            ]
        );
    }
}
