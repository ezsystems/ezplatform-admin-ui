<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
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

class LanguageController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\LanguageCreateMapper */
    private $languageCreateMapper;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        LanguageService $languageService,
        LanguageCreateMapper $languageCreateMapper,
        SubmitHandler $submitHandler,
        FormFactory $formFactory,
        ConfigResolverInterface $configResolver
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->languageService = $languageService;
        $this->languageCreateMapper = $languageCreateMapper;
        $this->submitHandler = $submitHandler;
        $this->formFactory = $formFactory;
        $this->configResolver = $configResolver;
    }

    /**
     * Renders the language list.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($this->languageService->loadLanguages())
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination.language_limit'));
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var \eZ\Publish\API\Repository\Values\Content\Language[] $languageList */
        $languageList = $pagerfanta->getCurrentPageResults();

        $deleteLanguagesForm = $this->formFactory->deleteLanguages(
            new LanguagesDeleteData($this->getLanguagesNumbers($languageList))
        );

        return $this->render('@ezdesign/language/list.html.twig', [
            'pager' => $pagerfanta,
            'form_languages_delete' => $deleteLanguagesForm->createView(),
            'can_administrate' => $this->isGranted(new Attribute('content', 'translations')),
        ]);
    }

    /**
     * Renders the view of a language.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Language $language): Response
    {
        $deleteForm = $this->formFactory->deleteLanguage(
            new LanguageDeleteData($language)
        );

        return $this->render('@ezdesign/language/index.html.twig', [
            'language' => $language,
            'deleteForm' => $deleteForm->createView(),
            'can_administrate' => $this->isGranted(new Attribute('content', 'translations')),
        ]);
    }

    /**
     * Deletes a language.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
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
     * @param \eZ\Publish\API\Repository\Values\Content\Language[] $languages
     *
     * @return array
     */
    private function getLanguagesNumbers(array $languages): array
    {
        $languagesNumbers = array_column($languages, 'id');

        return array_combine($languagesNumbers, array_fill_keys($languagesNumbers, false));
    }
}
