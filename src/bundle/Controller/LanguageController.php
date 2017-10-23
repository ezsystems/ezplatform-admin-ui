<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\LanguageCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class LanguageController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var LanguageService */
    private $languageService;

    /** @var LanguageCreateMapper */
    private $languageCreateMapper;

    /** @var SubmitHandler $submitHandler */
    private $submitHandler;

    /** @var FormFactory */
    private $formFactory;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param LanguageService $languageService
     * @param LanguageCreateMapper $languageCreateMapper
     * @param SubmitHandler $submitHandler
     * @param FormFactory $formFactory
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        LanguageService $languageService,
        LanguageCreateMapper $languageCreateMapper,
        SubmitHandler $submitHandler,
        FormFactory $formFactory
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->languageService = $languageService;
        $this->languageCreateMapper = $languageCreateMapper;
        $this->submitHandler = $submitHandler;
        $this->formFactory = $formFactory;
    }

    /**
     * Renders the language list.
     *
     * @return Response
     */
    public function listAction(): Response
    {
        $languageList = $this->languageService->loadLanguages();

        $deleteFormsByLanguageId = [];
        foreach ($languageList as $language) {
            $deleteFormsByLanguageId[$language->id] = $this->formFactory->deleteLanguage(
                new LanguageDeleteData($language),
                'ezplatform.language.list'
            )->createView();
        }

        return $this->render('@EzPlatformAdminUi/admin/language/list.html.twig', [
            'languageList' => $languageList,
            'deleteFormsByLanguageId' => $deleteFormsByLanguageId,
            'canEdit' => $this->isGranted(new Attribute('language', 'edit')),
            'canAssign' => $this->isGranted(new Attribute('language', 'assign')),
        ]);
    }

    /**
     * Renders the view of a language.
     *
     * @param Language $language
     *
     * @return Response
     */
    public function viewAction(Language $language): Response
    {
        $deleteForm = $this->formFactory->deleteLanguage(
            new LanguageDeleteData($language),
            'ezplatform.language.list'
        );

        return $this->render('@EzPlatformAdminUi/admin/language/view.html.twig', [
            'language' => $language,
            'deleteForm' => $deleteForm->createView(),
            'canEdit' => $this->isGranted(new Attribute('language', 'edit')),
            'canAssign' => $this->isGranted(new Attribute('language', 'assign')),
        ]);
    }

    /**
     * Deletes a language.
     *
     * @param Request $request
     * @param Language $language
     *
     * @return Response
     */
    public function deleteAction(Request $request, Language $language): Response
    {
        $form = $this->formFactory->deleteLanguage(
            new LanguageDeleteData($language),
            'ezplatform.language.list'
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LanguageDeleteData $data) {
                $language = $data->getLanguage();
                $this->languageService->deleteLanguage($language);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Language '%name%' removed.") */'language.delete.success',
                        ['%name%' => $language->name],
                        'language'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        /* Fallback Redirect */
        return $this->redirect($this->generateUrl('ezplatform.language.list'));
    }

    public function createAction(Request $request): Response
    {
        $form = $this->formFactory->createLanguage(
            null,
            'ezplatform.language.view'
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LanguageCreateData $data) {
                $languageCreateStruct = $this->languageCreateMapper->reverseMap($data);
                $language = $this->languageService->createLanguage($languageCreateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                    /** @Desc("Language '%name%' created.") */ 'language.create.success',
                        ['%name%' => $language->name],
                        'language'
                    )
                );

                return [
                    'languageId' => $language->id,
                ];
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/language/create.html.twig', [
            'form' => $form->createView(),
            'actionUrl' => $this->generateUrl('ezplatform.language.create'),
        ]);
    }

    public function editAction(Request $request, Language $language): Response
    {
        $form = $this->formFactory->updateLanguage(
            new LanguageUpdateData($language),
            'ezplatform.language.view'
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LanguageUpdateData $data) use ($language) {
                $this->languageService->updateLanguageName($language, $data->getName());

                $data->isEnabled()
                    ? $this->languageService->enableLanguage($language)
                    : $this->languageService->disableLanguage($language);

                $this->notificationHandler->success(
                    $this->translator->trans(
                    /** @Desc("Language '%name%' updated.") */ 'language.update.success',
                        ['%name%' => $language->name],
                        'language'
                    )
                );

                return [
                    'languageId' => $language->id,
                ];
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/language/edit.html.twig', [
            'form' => $form->createView(),
            'actionUrl' => $this->generateUrl('ezplatform.language.edit', ['languageId' => $language->id]),
            'language' => $language,
        ]);
    }
}
