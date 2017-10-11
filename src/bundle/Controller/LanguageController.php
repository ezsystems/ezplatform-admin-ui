<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\LanguageCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageController extends Controller
{
    /** @var LanguageService */
    protected $languageService;

    /** @var LanguageCreateMapper */
    protected $languageCreateMapper;

    /**
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService, LanguageCreateMapper $languageCreateMapper)
    {
        $this->languageService = $languageService;
        $this->languageCreateMapper = $languageCreateMapper;
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
            $deleteFormsByLanguageId[$language->id] = $this->createForm(
                LanguageDeleteType::class,
                new LanguageDeleteData($language)
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
     * @param mixed $languageId
     *
     * @return Response
     */
    public function viewAction(Language $language): Response
    {
        $deleteForm = $this->createForm(LanguageDeleteType::class, new LanguageDeleteData($language));

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
     * @return Response
     */
    public function deleteAction(Request $request, Language $language): Response
    {
        $form = $this->createForm(LanguageDeleteType::class, new LanguageDeleteData($language));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var LanguageDeleteData $data */
            $data = $form->getData();

            try {
                $this->languageService->deleteLanguage($data->getLanguage());
                $this->addFlash('success', 'language.deleted');
            } catch (InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($this->generateUrl('ezplatform.language.list'));
    }

    public function createAction(Request $request): Response
    {
        $form = $this->createForm(LanguageCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var LanguageCreateData $data */
            $data = $form->getData();

            $languageCreateStruct = $this->languageCreateMapper->reverseMap($data);
            $language = $this->languageService->createLanguage($languageCreateStruct);

            $this->addFlash('success', 'language.create.success');

            return $this->redirect($this->generateUrl('ezplatform.language.view', ['languageId' => $language->id]));
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->render('@EzPlatformAdminUi/admin/language/create.html.twig', [
            'form' => $form->createView(),
            'actionUrl' => $this->generateUrl('ezplatform.language.create'),
        ]);
    }

    public function editAction(Request $request, Language $language): Response
    {
        $form = $this->createForm(LanguageUpdateType::class, new LanguageUpdateData($language));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var LanguageUpdateData $data */
            $data = $form->getData();

            $this->languageService->updateLanguageName($language, $data->getName());

            $data->isEnabled()
                ? $this->languageService->enableLanguage($language)
                : $this->languageService->disableLanguage($language);

            $this->addFlash('success', 'language.update.success');

            return $this->redirect($this->generateUrl('ezplatform.language.view', ['languageId' => $language->id]));
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->render('@EzPlatformAdminUi/admin/language/edit.html.twig', [
            'form' => $form->createView(),
            'actionUrl' => $this->generateUrl('ezplatform.language.edit', ['languageId' => $language->id]),
            'language' => $language,
        ]);
    }
}
