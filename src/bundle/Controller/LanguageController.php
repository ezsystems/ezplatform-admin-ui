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
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguagesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\LanguageCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException as TranslationInvalidArgumentException;

class LanguageController extends Controller
{
    /** @var TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var LanguageService */
    private $languageService;

    /** @var LanguageCreateMapper */
    private $languageCreateMapper;

    /** @var SubmitHandler $submitHandler */
    private $submitHandler;

    /** @var FormFactory */
    private $formFactory;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param TranslatableNotificationHandlerInterface $notificationHandler
     * @param LanguageService $languageService
     * @param LanguageCreateMapper $languageCreateMapper
     * @param SubmitHandler $submitHandler
     * @param FormFactory $formFactory
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        LanguageService $languageService,
        LanguageCreateMapper $languageCreateMapper,
        SubmitHandler $submitHandler,
        FormFactory $formFactory,
        int $defaultPaginationLimit
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->languageService = $languageService;
        $this->languageCreateMapper = $languageCreateMapper;
        $this->submitHandler = $submitHandler;
        $this->formFactory = $formFactory;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * Renders the language list.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($this->languageService->loadLanguages())
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var Language[] $languageList */
        $languageList = $pagerfanta->getCurrentPageResults();

        $deleteLanguagesForm = $this->formFactory->deleteLanguages(
            new LanguagesDeleteData($this->getLanguagesNumbers($languageList))
        );

        return $this->render('@ezdesign/language/list.html.twig', [
            'pager' => $pagerfanta,
            'form_languages_delete' => $deleteLanguagesForm->createView(),
            /* @deprecated since version 2.2, to be removed in 3.0. Use 'can_administrate' instead. */
            'canEdit' => $this->isGranted(new Attribute('content', 'translations')),
            /* @deprecated since version 2.2, to be removed in 3.0. Use 'can_administrate' instead. */
            'canAssign' => $this->isGranted(new Attribute('content', 'translations')),
            'can_administrate' => $this->isGranted(new Attribute('content', 'translations')),
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
            new LanguageDeleteData($language)
        );

        return $this->render('@ezdesign/language/index.html.twig', [
            'language' => $language,
            'deleteForm' => $deleteForm->createView(),
            /* @deprecated since version 2.2, to be removed in 3.0. Use 'can_administrate' instead. */
            'canEdit' => $this->isGranted(new Attribute('content', 'translations')),
            /* @deprecated since version 2.2, to be removed in 3.0. Use 'can_administrate' instead. */
            'canAssign' => $this->isGranted(new Attribute('content', 'translations')),
            'can_administrate' => $this->isGranted(new Attribute('content', 'translations')),
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
            new LanguageDeleteData($language)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LanguageDeleteData $data) {
                $language = $data->getLanguage();
                $this->languageService->deleteLanguage($language);

                $this->notificationHandler->success(
                    /** @Desc("Language '%name%' removed.") */
                    'language.delete.success',
                    ['%name%' => $language->name],
                    'language'
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.language.list'));
    }

    /**
     * Handles removing languages based on submitted form.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws UnauthorizedException
     * @throws InvalidOptionsException
     * @throws TranslationInvalidArgumentException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws \InvalidArgumentException
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->deleteLanguages(
            new LanguagesDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LanguagesDeleteData $data) {
                foreach ($data->getLanguages() as $languageId => $selected) {
                    $language = $this->languageService->loadLanguageById($languageId);
                    $this->languageService->deleteLanguage($language);

                    $this->notificationHandler->success(
                        /** @Desc("Language '%name%' removed.") */
                        'language.delete.success',
                        ['%name%' => $language->name],
                        'language'
                    );
                }
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.language.list'));
    }

    public function createAction(Request $request): Response
    {
        $form = $this->formFactory->createLanguage();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LanguageCreateData $data) {
                $languageCreateStruct = $this->languageCreateMapper->reverseMap($data);
                $language = $this->languageService->createLanguage($languageCreateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Language '%name%' created.") */
                    'language.create.success',
                    ['%name%' => $language->name],
                    'language'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.language.view', [
                    'languageId' => $language->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/language/create.html.twig', [
            'form' => $form->createView(),
            'actionUrl' => $this->generateUrl('ezplatform.language.create'),
        ]);
    }

    public function editAction(Request $request, Language $language): Response
    {
        $form = $this->formFactory->updateLanguage(
            new LanguageUpdateData($language)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (LanguageUpdateData $data) use ($language) {
                $this->languageService->updateLanguageName($language, $data->getName());

                $data->isEnabled()
                    ? $this->languageService->enableLanguage($language)
                    : $this->languageService->disableLanguage($language);

                $this->notificationHandler->success(
                    /** @Desc("Language '%name%' updated.") */
                    'language.update.success',
                    ['%name%' => $language->name],
                    'language'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.language.view', [
                    'languageId' => $language->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/language/edit.html.twig', [
            'form' => $form->createView(),
            'actionUrl' => $this->generateUrl('ezplatform.language.edit', ['languageId' => $language->id]),
            'language' => $language,
        ]);
    }

    /**
     * @param Language[] $languages
     *
     * @return array
     */
    private function getLanguagesNumbers(array $languages): array
    {
        $languagesNumbers = array_column($languages, 'id');

        return array_combine($languagesNumbers, array_fill_keys($languagesNumbers, false));
    }
}
